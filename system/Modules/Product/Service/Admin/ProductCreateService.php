<?php

namespace Modules\Product\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Product\App\Events\ProductCreated;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductOption;
use Modules\Product\App\Models\ProductOptionValue;
use Modules\Product\App\Models\ProductVariant;
use Modules\Product\DTO\ProductCreateDTO;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ProductCreateService
{
    function create(ProductCreateDTO $productCreateDTO, $ipAddress)
    {
        try {
            DB::beginTransaction();

            $product = $this->createProduct($productCreateDTO);

            if (!empty($productCreateDTO->options)) {
                $productOptionNames = $this->createProductOptions($product->id, $productCreateDTO->options);
            }

            if (!empty($productCreateDTO->variants)) {
                $this->createProductVariants($product->id, $productCreateDTO->variants, $productOptionNames ?? null);
            }

            if (!empty($productCreateDTO->attributes)) {
                $this->createProductAttributes($product, $productCreateDTO->attributes);
            }

            if (!empty($productCreateDTO->couponId)) {
                $this->createProductCoupons($product, $productCreateDTO->couponId);
            }
            if (!empty($productCreateDTO->keySpecs)) {
                $this->createProductKeySpecs($product, $productCreateDTO->keySpecs);
            }

            if (!empty($productCreateDTO->featureId)) {
                $product->features()->sync($productCreateDTO->featureId);
            }

            if (!empty($productCreateDTO->activeOfferId)) {
                $product->activeOffers()->sync($productCreateDTO->activeOfferId);
            }

            $this->logProductCreation($product, $ipAddress);

            DB::commit();

            // Dispatch ProductCreated event to trigger cache invalidation
            // Event::dispatch(new ProductCreated($product));

        } catch (\Exception $exception) {
            Log::error('Error creating product', [
                'message' => $exception->getMessage(),
                'product_name' => $productCreateDTO->productName ?? null,
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
    private function createProduct($productCreateDTO)
    {
        try {
            $maxSortOrder = Product::max('sort_order');

            return Product::create([
                'uuid' => Str::uuid(),
                'product_name' => $productCreateDTO->productName,
                'slug' => $productCreateDTO->url,
                'brand_id' => $productCreateDTO->brandId,
                'sort_order' => $maxSortOrder + 1,
                'best_seller' => $productCreateDTO->bestSeller,
                'has_variant' => $productCreateDTO->hasVariant,
                'original_price' => $productCreateDTO->originalPrice,
                'special_price' => $productCreateDTO->specialPrice,
                'special_price_start' => $productCreateDTO->specialPriceStart,
                'special_price_end' => $productCreateDTO->specialPriceEnd,
                'sku' => $productCreateDTO->sku,
                'description' => $productCreateDTO->description,
                'additional_description' => $productCreateDTO->additionalDescription,
                'status' => $productCreateDTO->status,
                'sale_start' => $productCreateDTO->saleStart,
                'sale_end' => $productCreateDTO->saleEnd,
                'quantity' => $productCreateDTO->quantity,
		'weight' => $productCreateDTO->weight,
                'in_stock' => $productCreateDTO->inStock,
                'new_from' => $productCreateDTO->newFrom,
                'new_to' => $productCreateDTO->newTo,
                'specifications' => $productCreateDTO->specifications
            ]);
        } catch (\Exception $exception) {
            Log::error('Error creating product', [
                'message' => $exception->getMessage(),
                'product_name' => $productCreateDTO->productName,
            ]);
            throw $exception;
        }
    }
    private function createProductOptions(int $id, array $options)
    {
        try {
            $productOptionLimit = ProductOptionLimitationService::getProductOptionLimit();

            if (count($options) > $productOptionLimit) {
                Log::warning('Product option limit exceeded', ['product_id' => $id, 'option_count' => count($options), 'limit' => $productOptionLimit]);
                throw new Exception('A maximum of ' . $productOptionLimit . ' options can be added.', ErrorCode::UNPROCESSABLE_CONTENT);
            }

            $createdProductOptions = [];
            $imageOptionCount = 0;

            foreach ($options as $option) {
                if ($option['hasImage']) {
                    $imageOptionCount++;
                    if ($imageOptionCount > 1) {
                        Log::warning('Multiple image options detected', ['product_id' => $id]);
                        throw new Exception('A product can only contain one option with images.', ErrorCode::FORBIDDEN);
                    }
                }

                $productOption = ProductOption::create([
                    'uuid' => Str::uuid()->toString(),
                    'name' => $option['name'],
                    'has_image' => $option['hasImage'],
                    'product_id' => $id
                ]);

                $createdProductOptions[] = $productOption->name;

                foreach ($option['values'] as $optionValue) {
                    $productOptionValue = ProductOptionValue::create([
                        'uuid' => Str::uuid()->toString(),
                        'product_option_id' => $productOption->id,
                        'option_name' => $optionValue['optionName']
                    ]);

                    try {
                        if (isset($optionValue['files'])) {
                            $images = collect($optionValue['files']);

                            $baseImage = $images->get('baseImage');

                            if ($baseImage) {
                                $productOptionValue->saveFile($baseImage, 'baseImage', $productOptionValue->id);
                            }

                            $additionalImages = $images->get('additionalImage', []);
                            foreach ($additionalImages as $additionalImage) {
                                $productOptionValue->saveFile($additionalImage, 'additionalImage', $productOptionValue->id);
                            }
                        }
                    } catch (\Exception $exception) {
                        Log::error('Error saving product option value files', [
                            'message' => $exception->getMessage(),
                            'product_id' => $id,
                            'option_value_id' => $productOptionValue->id,
                        ]);
                        throw $exception;
                    }
                }
            }

            Log::info('Product options created', ['product_id' => $id, 'option_count' => count($createdProductOptions)]);
            return $createdProductOptions;
        } catch (\Exception $exception) {
            Log::error('Error creating product options', [
                'message' => $exception->getMessage(),
                'product_id' => $id,
            ]);
            throw $exception;
        }
    }
    private function createProductVariants(int $productId, array $variants, $productOptionNames)
    {
        try {
            $product = Product::findOrFail($productId);

            foreach ($variants as $variantIndex => $variantData) {
                if (!isset($variantData['optionValues']) || !is_array($variantData['optionValues'])) {
                    continue;
                }

                $variant = ProductVariant::create([
                    'uuid' => Str::uuid(),
                    'name' => $variantData['name'] ?? 'Unnamed Variant',
                    'sku' => $variantData['sku'] ?? $product->sku . '-var-' . ($variantIndex + 1),
                    'status' => $variantData['status'] ?? true,
                    'original_price' => $variantData['originalPrice'] ?? 0,
                    'special_price' => $variantData['specialPrice'] ?? null,
                    'special_price_start' => $variantData['specialPriceStart'] ?? null,
                    'special_price_end' => $variantData['specialPriceEnd'] ?? null,
                    'quantity' => $variantData['quantity'] ?? 0,
                    'in_stock' => $variantData['inStock'] ?? false,
                    'product_id' => $productId
                ]);

                $attachedOptions = 0;
                foreach ($variantData['optionValues'] as $optionName => $valueName) {
                    try {
                        $optionValue = ProductOptionValue::whereHas('option', function($query) use ($productId, $optionName) {
                            $query->where('product_id', $productId)
                                ->where('name', $optionName);
                        })
                            ->where('option_name', $valueName)
                            ->first();

                        if ($optionValue) {
                            DB::table('product_option_variants')->insert([
                                'product_id' => $productId,
                                'product_option_value_id' => $optionValue->id,
                                'product_variant_id' => $variant->id,
                            ]);
                            $attachedOptions++;
                        }
                    } catch (\Exception $e) {
                        Log::error('Error attaching option value', [
                            'product_id' => $productId,
                            'variant_id' => $variant->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

        } catch (\Exception $exception) {
            Log::error('Product variant creation failed', [
                'product_id' => $productId,
                'error' => $exception->getMessage()
            ]);
            throw new \RuntimeException('Variant creation failed: ' . $exception->getMessage());
        }
    }
    private function createProductAttributes(Product $product, array $attributes)
    {
        try {
            foreach ($attributes as $attributeData) {
                $productAttributes = $product->attributes()->updateOrCreate(
                    ['attribute_id' => $attributeData['attributeId']],
                    ['product_id' => $product->id]
                );

                foreach ($attributeData['values'] as $value) {
                    $productAttributes->values()->updateOrCreate(
                        ['attribute_value_id' => $value],
                        ['attribute_value_id' => $value]
                    );
                }
            }
        } catch (\Exception $exception) {
            Log::error('Error creating product attributes', [
                'message' => $exception->getMessage(),
                'product_id' => $product->id,
            ]);
            throw $exception;
        }
    }

    private function createProductCoupons(Product $product, array $couponsId)
    {
        try {
            $data = [];
            foreach ($couponsId as $id) {
                $data[] = [
                    'product_id' => $product->id,
                    'coupon_id' => $id,
                ];
            }

            DB::table('product_coupons')->insert($data);
        } catch (\Exception $exception) {
            Log::error('Error creating product coupons', [
                'message' => $exception->getMessage(),
                'product_id' => $product->id,
            ]);
            throw $exception;
        }
    }
    private function createProductKeySpecs(Product $product, array $keySpecs)
    {
        try {
            foreach ($keySpecs as $keySpec) {
                $product->keySpecs()->create([
                    'spec_key' => $keySpec['key'],
                    'spec_value' => $keySpec['value']
                ]);
            }
        } catch (\Exception $exception) {
            Log::error('Error creating product key specs', [
                'message' => $exception->getMessage(),
                'product_id' => $product->id,
            ]);
            throw $exception;
        }
    }


    private function logProductCreation(Product $product, string $ipAddress)
    {
        try {
            Event::dispatch(
                new AdminUserActivityLogEvent(
                    'Product created of name: ' . $product->product_name,
                    $product->id,
                    ActivityTypeConstant::PRODUCT_CREATED,
                    $ipAddress
                )
            );
        } catch (\Exception $exception) {
            Log::error('Error logging product creation', [
                'message' => $exception->getMessage(),
                'product_id' => $product->id,
                'ip_address' => $ipAddress,
            ]);
            throw $exception;
        }
    }
}
