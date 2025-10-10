<?php

namespace Modules\Product\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Product\App\Events\ProductUpdated;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductOption;
use Modules\Product\App\Models\ProductOptionValue;
use Modules\Product\App\Models\ProductVariant;
use Modules\Product\DTO\ProductUpdateDTO;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\Shared\Services\CacheService;

class ProductUpdateService
{
    private CacheService $cacheService;

    function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    function update(ProductUpdateDTO $productUpdateDTO, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $oldProductName = Product::where('uuid', $productUpdateDTO->uuid)->value('product_name');

            $product = $this->updateProduct($productUpdateDTO);

            $this->updateProductOptions($productUpdateDTO->options, $product);

            $this->updateVariants($productUpdateDTO->variants, $product);

            $this->updateAttributes($productUpdateDTO->attributes, $product);

            if (!empty($productUpdateDTO->couponId)) {
                $this->updateCoupons($productUpdateDTO->couponId, $product);
            }
            if (property_exists($productUpdateDTO, 'keySpecs') && is_array($productUpdateDTO->keySpecs)) {
                $this->updateProductKeySpecs($productUpdateDTO->keySpecs, $product);
            }

            $product->features()->sync($productUpdateDTO->featureId);

            $product->activeOffers()->sync($productUpdateDTO->activeOfferId);

            $this->updateSpecifications($productUpdateDTO->specifications, $product);

            $this->logProductUpdate($oldProductName, $product, $ipAddress);

            DB::commit();

            // Dispatch ProductUpdated event to trigger cache invalidation
            Event::dispatch(new ProductUpdated($product));

        } catch (\Exception $exception) {
            Log::error('Error updating product: ' . $exception->getMessage(), [
                'exception' => $exception,
                'product_uuid' => $productUpdateDTO->uuid,
                'ip_address' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
    private function updateProduct(ProductUpdateDTO $productUpdateDTO)
    {
        $product = Product::where('uuid', $productUpdateDTO->uuid)->first();

        if (!$product) {
            throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
        }

        $product->update([
            'product_name' => $productUpdateDTO->productName,
            'slug' => $productUpdateDTO->url,
            'brand_id' => $productUpdateDTO->brandId,
            'sort_order' => $productUpdateDTO->sortOrder,
            'best_seller' => $productUpdateDTO->bestSeller,
            'has_variant' => $productUpdateDTO->hasVariant,
            'description' => $productUpdateDTO->description,
            'status' => $productUpdateDTO->status,
            'original_price' => $productUpdateDTO->originalPrice,
            'special_price' => $productUpdateDTO->specialPrice,
            'special_price_start' => $productUpdateDTO->specialPriceStart,
            'special_price_end' => $productUpdateDTO->specialPriceEnd,
            'sku' => $productUpdateDTO->sku,
            'quantity' => $productUpdateDTO->quantity,
            'in_stock' => $productUpdateDTO->inStock,
            'additional_description' => $productUpdateDTO->additionalDescription,
            'sale_start' => $productUpdateDTO->saleStart,
            'sale_end' => $productUpdateDTO->saleEnd,
            'new_from' => $productUpdateDTO->newFrom,
            'new_to' => $productUpdateDTO->newTo,
        ]);

        return $product;
    }

    private function updateProductOptions($options, $product)
    {
        $existingProductOptions = $product->options->keyBy('uuid');
        $colorOptionCount = 0;
        $deletedOptionUuids = [];

        foreach ($existingProductOptions as $existingOption) {
            if (!in_array($existingOption->uuid, array_column($options, 'uuid'))) {
                $deletedOptionUuids[] = $existingOption->uuid;
                $existingOption->delete();
            }
        }

        $product->refresh();

        foreach ($options as $option) {

            if ($option['isColor']) {
                $colorOptionCount++;
                if ($colorOptionCount > 1) {
                    throw new Exception('A product can only contain the color option once.', ErrorCode::FORBIDDEN);
                }
            }

            if (!empty($option['uuid'])) {
                if (in_array($option['uuid'], $deletedOptionUuids)) {
                    // Option is being deleted, skip update
                    continue;
                }
                $this->updateExistingOption($option, $existingProductOptions, $product);
            } else {
                $this->createNewOption($option, $product);
            }
        }
    }

    private function updateExistingOption($option, $existingProductOptions, $product)
    {
        $productOption = $existingProductOptions->where('uuid', $option['uuid'])
            ->select('id', 'name')
            ->first();

        if (!$productOption) {
            throw new Exception("Option not found for product: $product->product_name", ErrorCode::NOT_FOUND);
        }

        $existingProductOption = $existingProductOptions->where('id', $productOption['id'])->first();
        $existingProductOption->update(['name' => $option['name'], 'has_image' => $option['hasImage']]);

        $this->updateOptionValues($option, $option['values'], $existingProductOption);
    }

    private function updateOptionValues($option, $values, $existingProductOption)
    {
        $optionValue = $existingProductOption->values->keyBy('uuid');;

        foreach ($optionValue as $optValue) {
            if (!in_array($optValue->uuid, array_column($option['values'], 'uuid'))) {
                $optValue->delete();
            }
        }

        foreach ($values as $value) {
            $valueId = $this->getValueIdToUpdate($option, $value, $optionValue);
            $existingOptionValue = $optionValue->where('id', $valueId)->first();

            if ($existingOptionValue) {
                if (!empty($value['files'])) {
                    $this->handleFiles($existingOptionValue, $value['files'], $valueId);
                }
            } else {
                $this->createNewOptionValue($value, $existingProductOption);
            }
        }
    }
    private function updateProductKeySpecs(array $keySpecs, Product $product) {
        try {
            $product->keySpecs()->delete();
            foreach ($keySpecs as $keySpec) {
                $product->keySpecs()->create([
                    'spec_key' => $keySpec['key'],
                    'spec_value' => $keySpec['value']
                ]);
            }
        } catch (\Exception $exception) {
            Log::error('Error updating product key specs', [
                'message' => $exception->getMessage(),
                'product_id' => $product->id,
            ]);
            throw $exception;
        }
    }



    private function getValueIdToUpdate($option, $value, $optionValue)
    {
        if (!empty($value['uuid'])) {
            $optionValueId = $optionValue->where('uuid', $value['uuid'])
                ->value('id');

            if (!$optionValueId) {
                throw new Exception("Value not found for option: {$option['name']}", ErrorCode::NOT_FOUND);
            }

            return $optionValueId;
        }

        return '';
    }

    private function createNewOption($option, $product)
    {
        $productOptionLimit = ProductOptionLimitationService::getProductOptionLimit();

        if ($product->options->count() >= $productOptionLimit) {
            throw new Exception('A maximum of ' . $productOptionLimit . ' options can be added.', ErrorCode::UNPROCESSABLE_CONTENT);
        }

        $newOption = $product->options()->create([
            'uuid' => Str::uuid()->toString(),
            'name' => $option['name'],
            'has_image' => $option['hasImage'],
        ]);

        $this->createOptionValues($option['values'], $newOption);
    }

    private function createOptionValues($values, $newOption)
    {
        foreach ($values as $value) {
            $newProductOptionValues = $newOption->values()->create([
                'uuid' => Str::uuid()->toString(),
                'option_name' => $value['optionName'],
                'option_data' => $value['optionData'],
            ]);

            if (!empty($value['files'])) {
                $this->handleFiles($newProductOptionValues, $value['files'], $newProductOptionValues->id);
            }
        }
    }

    private function createNewOptionValue($value, $existingProductOption)
    {
        $newOptionValue = new ProductOptionValue([
            'uuid' => Str::uuid()->toString(),
            'option_name' => $value['optionName'],
        ]);

        $existingProductOption->values()->save($newOptionValue);

        if (!empty($value['files'])) {
            $this->handleFiles($newOptionValue, $value['files'], $newOptionValue->id);
        }
    }

    private function handleFiles($entity, $files, $valueId)
    {
        foreach ($files as $zone => $fileId) {
            if ($zone === 'baseImage') {
                if (!$fileId) {
                    throw new Exception('Base image is required', ErrorCode::BAD_REQUEST);
                }

                $this->updateOrInsertModelFile($entity, $valueId, $zone, $fileId);
            } elseif ($zone === 'additionalImage') {
                if (is_array($fileId)) {
                    $this->handleAdditionalImages($entity, $valueId, $zone, $fileId);
                } else {
                    $this->updateOrInsertModelFile($entity, $valueId, $zone, $fileId);
                }
            }
        }
    }

    private function handleAdditionalImages($entity, $valueId, $zone, $fileId)
    {
        $existingAdditionalImageIds = DB::table('model_files')
            ->where('model_id', $valueId)
            ->where('zone', $zone)
            ->pluck('file_id')
            ->toArray();

        $filesToDelete = array_diff($existingAdditionalImageIds, $fileId);
        if (!empty($filesToDelete)) {
            DB::table('model_files')
                ->where('model_id', $valueId)
                ->where('zone', $zone)
                ->whereIn('file_id', $filesToDelete)
                ->delete();
        }

        foreach ($fileId as $additionalFileId) {
            $this->updateOrInsertModelFile($entity, $valueId, $zone, $additionalFileId);
        }
    }
    private function updateOrInsertModelFile($entity, $valueId, $zone, $fileId)
    {
        DB::table('model_files')->updateOrInsert(
            [
                'model_id' => $valueId,
                'zone' => $zone,
                'file_id' => $fileId,
            ],
            [
                'model_id' => $valueId,
                'model_type' => get_class($entity),
                'zone' => $zone,
                'file_id' => $fileId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function updateVariants(array $variants, Product $product)
    {
        try {
            ProductVariant::where('product_id', $product->id)->delete();

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
                    'product_id' => $product->id
                ]);

                $attachedOptions = 0;
                foreach ($variantData['optionValues'] as $optionName => $valueName) {
                    try {
                        $optionValue = ProductOptionValue::whereHas('option', function($query) use ($product, $optionName) {
                            $query->where('product_id', $product->id)
                                ->where('name', $optionName);
                        })
                            ->where('option_name', $valueName)
                            ->first();

                        if ($optionValue) {
                            DB::table('product_option_variants')->insert([
                                'product_id' => $product->id,
                                'product_option_value_id' => $optionValue->id,
                                'product_variant_id' => $variant->id,
                            ]);
                            $attachedOptions++;
                        }
                    } catch (\Exception $e) {
                        Log::error('Error attaching option value', [
                            'product_id' => $product->id,
                            'variant_id' => $variant->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        } catch (\Exception $exception) {
            Log::error('Error updating product variants', [
                'message' => $exception->getMessage(),
                'product_id' => $product->id,
            ]);
            throw $exception;
        }
    }
    private function updateAttributes(array $attributes, Product $product)
    {
        $existingAttributeIds = $product->attributes()->pluck('id')->toArray();

        $keepAttributeIds = [];
        foreach ($attributes as $attributeData) {
            if (isset($attributeData['attributeId'])) {
                $conditions = isset($attributeData['id']) ? ['id' => $attributeData['id']] : ['attribute_id' => $attributeData['attributeId']];

                $productAttribute = $product->attributes()->updateOrCreate(
                    $conditions,
                    ['attribute_id' => $attributeData['attributeId'], 'product_id' => $product->id]
                );

                $keepAttributeIds[] = $productAttribute->id;

                if (!empty($attributeData['values'])) {
                    $existingValues = $productAttribute->values->pluck('attribute_value_id')->toArray();
                    $newValues = $attributeData['values'];

                    $valuesToCreate = array_diff($newValues, $existingValues);
                    $valuesToDelete = array_diff($existingValues, $newValues);

                    foreach ($valuesToCreate as $value) {
                        $productAttribute->values()->create(['attribute_value_id' => $value]);
                    }

                    $productAttribute->values()
                        ->whereIn('attribute_value_id', $valuesToDelete)
                        ->delete();
                }
            }
        }

        $attributesToDelete = array_diff($existingAttributeIds, $keepAttributeIds);
        if (!empty($attributesToDelete)) {
            $product->attributes()->whereIn('id', $attributesToDelete)->delete();
        }
    }

    private function updateCoupons(array $couponIds, Product $product)
    {
        DB::table('product_coupons')->where('product_id', $product->id)
            ->delete();

        $data = array_map(function($couponId) use ($product) {
            return [
                'product_id' => $product->id,
                'coupon_id' => $couponId,
            ];
        }, $couponIds);

        if (!empty($data)) {
            DB::table('product_coupons')->insert($data);
        }
    }

    private function updateSpecifications($newSpecifications, $product)
    {
        $filteredSpecifications = array_filter($newSpecifications, function ($spec) {
            return !empty($spec['content']);
        });

        $uniqueSpecifications = array_unique($filteredSpecifications, SORT_REGULAR);

        try {
            $product->update(['specifications' => array_values($uniqueSpecifications)]);
        } catch (\Exception $exception) {
            Log::error('Error updating product specifications: ' . $exception->getMessage());
            throw $exception;
        }
    }

    private function logProductUpdate($oldProductName, Product $product, $ipAddress)
    {
        $description = sprintf(
            "Updated product: %s (ID: %d)",
            Str::limit($product->product_name, 50),
            $product->id
        );

        try {
            Event::dispatch(
                new AdminUserActivityLogEvent(
                    $description,
                    $product->id,
                    ActivityTypeConstant::PRODUCT_UPDATED,
                    $ipAddress
                )
            );
        } catch (\Throwable $e) {
            Log::error('Product update logging failed', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'old_name' => $oldProductName,
            ]);
        }
    }
}
