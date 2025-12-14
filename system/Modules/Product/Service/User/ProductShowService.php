<?php

namespace Modules\Product\Service\User;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Coupon\App\Models\Coupon;
use Modules\Product\App\Models\Product;
use Modules\Product\Trait\ValidateProductTrait;
use Modules\Shared\Exception\Exception;
use Modules\Shared\Services\CacheService;
use Modules\Shared\StatusCode\ErrorCode;
use Illuminate\Http\Request;

class ProductShowService
{
    use ValidateProductTrait;

    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function show(Request $request, string $slug)
    {
        $productFromSlug = $this->validateProduct($slug);
        $productId = $productFromSlug->id;

        // $cacheKey = $this->cacheService->generateProductKey($productId);
        // $cacheTags = ["product:{$productId}"];

        // $cachedData = $this->cacheService->get($cacheKey, $cacheTags);

        // if ($cachedData) {
        //     return [$cachedData];
        // }

        $product = Product::with([
            'categories:id,name',
            'meta',
            'files',
            'keySpecs:id,product_id,spec_key,spec_value',
            'features:id,text',
            'activeOffers.files',
            'darazCount',
            'options.values.files',
            'reviews' => fn($q) => $q->where('type', 'review')->where('is_approved', true),
            'relatedProducts' => fn($q) => $q->with(['files', 'reviews', 'options.values.files', 'variants.optionValues.files'])
        ])->find($productId);

        if (! $product instanceof Product) {
            throw new Exception('Product not found or invalid product data.', ErrorCode::NOT_FOUND);
        }

        $activeSpecialPrice = $this->validateSpecialPrice($product);
        $discountPercentage = $this->calculatePriceDifferencePercentage($product, null, $activeSpecialPrice);
        $keySpecsData = $this->getKeySpecsData($product);
        $specificationData = $this->getSpecificationsData($product);
        $relatedProductsData = $this->getRelatedProducts($product, $productId);

        // Calculate review data
        $reviewData = $this->calculateReviewData($product);

        $responseData = [
            'uuid' => $product->uuid,
            'productName' => $product->product_name,
            'brandId' => $product->brand_id,
            'url' => $product->slug,
            'sortOrder' => $product->sort_order ?? 0,
            'bestSeller' => (bool)$product->best_seller,
            'hasVariant' => (bool)$product->has_variant,
            'originalPrice' => $product->original_price,
            'specialPrice' =>  $activeSpecialPrice,
            'specialPriceStart' => $product->special_price_start,
            'specialPriceEnd' => $product->special_price_end,
            'discountPercentage' => $discountPercentage,
            'sku' => $product->sku,
            'description' => $product->description,
            'additionalDescription' => $product->additional_description ?? '',
            'status' => (bool)$product->status,
            'quantity' => $product->quantity ?? 0,
            'inStock' => (bool)$product->in_stock,
            'soldCount' => $product->total_completed_sold ?? 0,
            'newFrom' => $product->new_from ?? '',
            'newTo' => $product->new_to ?? '',
            'review_count' => $reviewData['count'],
            'average_rating' => $reviewData['average'],
            'options' => $this->getOptionsData($product),
            'meta' => $this->getMetaData($product),
            'attributes' => [], // This was empty before, remains empty.
            'files' => $this->getMediaFiles($product),
            'keySpecs' => $keySpecsData,
            'specifications' => $specificationData,
            'features' => $this->getFeaturesData($product),
            'activeOffers' => $this->getActiveOffersData($product),
            'darazCount' => $this->getDarazCountData($product),
            'categories' => $this->getProductCategories($product),
            'relatedProducts' => $relatedProductsData,
            'upSellProducts' => [],
            'crossSellProducts' => [],
            'couponData' => $this->getApplicableCouponsData($product)
        ];

        // $this->cacheService->put($cacheKey, $responseData, $this->cacheService->getProductDetailTtl(), $cacheTags);

        return [$responseData];
    }

    private function getProductCategories(Product $product): array
    {
        return $product->categories->map(function($cat) {
            return ['id' => $cat->id, 'name' => $cat->name];
        })->toArray();
    }

    // Modified getRelatedProducts to include caching
    private function getRelatedProducts(Product $product, int|string $parentProductId)
    {
        // $cacheKey = $this->cacheService->generateProductRelatedKey($parentProductId);
        // Tag with the parent product ID so it can be invalidated when the parent changes,
        // or when any of the related products themselves change (more complex, for now parent-based invalidation).
        // $cacheTags = ['product:' . $parentProductId, 'product:' . $parentProductId . ':related'];


        // $cachedRelatedProducts = $this->cacheService->get($cacheKey, $cacheTags);

        // if ($cachedRelatedProducts !== null) {
        //     return $cachedRelatedProducts;
        // }

        $relatedProducts = $product->relatedProducts()->get();
        $relatedProductData = [];

        foreach ($relatedProducts as $relatedProduct) {
            $productVariant = null;
            $baseImage = null;

            if ($relatedProduct->has_variant) {
                $productVariant = $this->getFirstProductVariant($relatedProduct);

                if ($productVariant && $productVariant->optionValues->isNotEmpty()) {
                    $baseImage = $this->getBaseImageFromVariant($productVariant);
                    $validatedVariantSpecialPrice = $this->validateSpecialPrice($productVariant);
                }
            } else {
                $validatedRelatedProductSpecialPrice = $this->validateSpecialPrice($relatedProduct);
            }

            $relatedProductImage = $relatedProduct->filterFiles('baseImage')
                ->select(DB::raw("CONCAT(path, '/Thumbnail/', temp_filename) AS baseImage"))
                ->first();

            // Calculate review data for related product
            $relatedProductReviewData = $this->calculateReviewData($relatedProduct);

            $relatedProductData[] = [
                'name' => $relatedProduct->product_name,
                'slug' => $relatedProduct->slug,
                'bestSeller' => (bool)$relatedProduct->best_seller,
                'discountPercentage' => $this->calculatePriceDifferencePercentage(
                    $relatedProduct,
                    $productVariant,
                    $relatedProduct->has_variant && $productVariant ? $validatedVariantSpecialPrice : $validatedRelatedProductSpecialPrice
                ),
                'originalPrice' => $relatedProduct->has_variant && $productVariant ? $productVariant->original_price : $relatedProduct->original_price,
                'specialPrice' => $relatedProduct->has_variant && $productVariant ? $validatedVariantSpecialPrice : $validatedRelatedProductSpecialPrice,
                'status' => $relatedProduct->status,
                'inStock' => $relatedProduct->has_variant && $productVariant ? $productVariant->in_stock : $relatedProduct->in_stock,
                'quantity' => $relatedProduct->has_variant && $productVariant ? $productVariant->quantity : $relatedProduct->quantity,
                'soldCount' => $relatedProduct->total_completed_sold ?? 0,
                'review_count' => $relatedProductReviewData['count'],
                'average_rating' => $relatedProductReviewData['average'],
                'baseImage' => $relatedProductImage ? $relatedProductImage['baseImage'] : ($relatedProduct->has_variant ? $baseImage : null),
            ];
        }

        // $this->cacheService->put($cacheKey, $relatedProductData, $this->cacheService->getProductDetailTtl(), $cacheTags); // Same TTL as parent for simplicity

        return $relatedProductData;
    }
    
    private function getFirstProductVariant($product)
    {
        $productOptionId = $product->options()->where('has_image', true)->value('id');

        return $product->variants()->with('optionValues.files')
            ->whereHas('optionValues', function ($query) use ($productOptionId) {
                $query->where('product_option_id', $productOptionId);
            })->first(['id', 'original_price', 'special_price', 'special_price_start', 'special_price_end', 'in_stock', 'quantity']);
    }

    private function getBaseImageFromVariant($variant)
    {
        $file = $variant->optionValues->first()->filterFiles('baseImage')->first();
        if ($file) {
            return $file->path . '/Thumbnail/' . $file->temp_filename;
        } else {
            return null;
        }
    }
    
    private function getKeySpecsData($product)
    {
        $keySpecs = $product->keySpecs()
            ->select('id', 'spec_key as key', 'spec_value as value')
            ->get();

        return $keySpecs->map(function ($spec) {
            if (is_string($spec->value)) {
                $spec->value = json_decode($spec->value, true);
            }
            return $spec;
        })->toArray();
    }

    private function getSpecificationsData($product)
    {
        return $product->specifications;
    }


    private function getFileUrl($file, $size = 'Thumbnail'): ?string
    {
        if (!$file) return null;

        if (isset($file->url) && $file->url) {
            return $file->url;
        }

        if (isset($file->path) && isset($file->temp_filename)) {
            $basePath = rtrim(config('app.url'), '/');
            $path = ltrim($file->path, '/');

            if (strpos($path, $basePath) === 0) {
                $path = substr($path, strlen($basePath));
            }

            $path = ltrim($path, '/');
            $sizePath = ($size && $size !== 'original') ? "{$size}/" : "";
            $fileName = $file->temp_filename;

            return "{$basePath}/modules/files/{$sizePath}{$fileName}";
        }

        return null;
    }

    private function getMediaFiles(Product $product): array
    {
        $baseImageFile = $product->filterFiles('baseImage')->first();
        $additionalImageFiles = $product->filterFiles('additionalImage')->get();
        $descriptionVideoFile = $product->filterFiles('descriptionVideo')->first();

        return [
            'baseImage' => $baseImageFile ? [
                'url' => $this->getFileUrl($baseImageFile, 'Thumbnail')
            ] : null,
            'additionalImage' => $additionalImageFiles->map(function($file) {
                return $this->getFileUrl($file, 'Thumbnail');
            })->filter()->values()->all(),

            'descriptionVideo' => $descriptionVideoFile ? [
                'id' => $descriptionVideoFile->id,
                'url' => $this->getFileUrl($descriptionVideoFile, 'original')
            ] : null,
        ];
    }

    private function getMetaData(Product $product): array
    {
        $meta = $product->meta;
        if(!$meta) return ['metaTitle' => '', 'keywords' => [], 'metaDescription' => ''];

        $keywords = json_decode($meta->meta_keywords ?? '[]', true);
        return [
            'metaTitle' => $meta->meta_title ?? '',
            'keywords' => is_array($keywords) ? $keywords : [],
            'metaDescription' => $meta->meta_description ?? '',
        ];
    }

    private function getOptionsData(Product $product): array
    {
        if (!$product->has_variant) {
            return [];
        }

        $options = $product->options()
            ->select('id', 'uuid', 'name', 'has_image')
            ->with(['values.files'])
            ->get();

        return $options->map(function($option) {
            $values = $option->values->map(function($value) use ($option) {
                $valueData = [
                    'uuid' => $value->uuid,
                    'name' => $value->option_name,
                ];

                if ($option->has_image) {
                    $baseImageFile = $value->filterFiles('baseImage')->first();
                    $additionalImageFiles = $value->filterFiles('additionalImage')->get();

                    $allImageUrls = [];

                    if ($baseImageFile) {
                        $allImageUrls[] = $this->getFileUrl($baseImageFile, 'Thumbnail');
                    }

                    foreach ($additionalImageFiles as $additionalFile) {
                        $url = $this->getFileUrl($additionalFile, 'Thumbnail');
                        if ($url) $allImageUrls[] = $url;
                    }

                    $valueData['images'] = $allImageUrls;
                }

                return $valueData;
            })->toArray();

            return [
                'uuid' => $option->uuid,
                'name' => $option->name,
                'hasImage' => (bool)$option->has_image,
                'values' => $values,
            ];
        })->toArray();
    }

    private function getApplicableCouponsData(Product $product): array
    {
        // Get all active, public coupons that are applicable to this product
        $applicableCoupons = Coupon::where('is_active', true)
            ->where('is_public', true)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->where(function ($query) use ($product) {
                $query->where(function ($subQuery) use ($product) {
                    // Include coupons that specifically include this product
                    $subQuery->whereHas('products', function ($productQuery) use ($product) {
                        $productQuery->where('products.id', $product->id);
                    });
                })
                ->orWhere(function ($subQuery) use ($product) {
                    // Include coupons that don't have any specific product restrictions
                    $subQuery->whereDoesntHave('products')
                        ->whereDoesntHave('exclude');
                })
                ->orWhere(function ($subQuery) use ($product) {
                    // Include coupons that don't specifically exclude this product
                    $subQuery->whereDoesntHave('products')
                        ->whereDoesntHave('exclude', function ($excludeQuery) use ($product) {
                            $excludeQuery->where('products.id', $product->id);
                        });
                });
            })
            ->select([
                'id',
                'name',
                'code',
                'type',
                'value',
                'max_discount',
                'minimum_spend',
                'min_quantity',
                'end_date'
            ])
            ->get();

        return $applicableCoupons->map(function ($coupon) {
            // Handle end_date formatting safely
            $expiryDate = null;
            if ($coupon->end_date) {
                if ($coupon->end_date instanceof \Carbon\Carbon) {
                    $expiryDate = $coupon->end_date->format('M d, Y');
                } else {
                    try {
                        $expiryDate = \Carbon\Carbon::parse($coupon->end_date)->format('M d, Y');
                    } catch (\Exception $e) {
                        $expiryDate = $coupon->end_date;
                    }
                }
            }

            return [
                'id' => $coupon->id,
                'name' => $coupon->name,
                'couponCode' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'maxDiscount' => $coupon->max_discount,
                'minimumSpend' => $coupon->minimum_spend,
                'minQuantity' => $coupon->min_quantity,
                'isPercent' => $coupon->isPercentageType(),
                'expiryDate' => $expiryDate,
            ];
        })->toArray();
    }



    private function getActiveOffersData(Product $product): array
    {
        $activeOffers = $product->activeOffers()->where('is_active', true)->get();

        return $activeOffers->map(function($activeOffer) {
            $activeOfferIcon = $activeOffer->filterfiles('image')->first();

            return [
                'id' => $activeOffer->id,
                'text' => $activeOffer->text,
                'iconUrl' => $activeOfferIcon ? $this->getFileUrl($activeOfferIcon, 'Thumbnail') : null,
            ];
        })->toArray();
    }

    private function getFeaturesData(Product $product): array
    {
        $features = $product->features()->where('is_active', true)->get();

        return $features->map(function($feature) {
            return [
                'id' => $feature->id,
                'text' => $feature->text,
            ];
        })->toArray();
    }

    private function getDarazCountData(Product $product): array
    {
        $darazCountData = [];
        $darazCount = $product->darazCount()->where('is_active', true)->first();

        if ($darazCount) {
            $darazCountData['unitsSold'] = $darazCount->units_sold ?? 0;
            $darazCountData['reviewsCount'] = $darazCount->reviews_count ?? 0;
            $darazCountData['link'] = $darazCount->link ?? null;
        }

        return $darazCountData;
    }

    private function calculatePriceDifferencePercentage($product, $productVariant, $validatedSpecialPrice = null): int
    {
        $originalPrice = $product->original_price ?? ($productVariant ? $productVariant->original_price : null);
        $specialPrice = $validatedSpecialPrice ?? ($productVariant ? $productVariant->special_price : null);

        if ($originalPrice && $specialPrice && $originalPrice > $specialPrice) {
            $priceDifference = $originalPrice - $specialPrice;
            return round(($priceDifference / $originalPrice) * 100);
        }

        return 0;
    }

    private function validateSpecialPrice($product)
    {
        $now = Carbon::now();

        if (
            $product->special_price > 0 &&
            (!$product->special_price_start || $now->gte($product->special_price_start)) &&
            (!$product->special_price_end || $now->lte($product->special_price_end))
        ) {
            return $product->special_price;
        }

        return null;
    }

    private function calculateReviewData(Product $product): array
    {
        $reviews = $product->reviews()
            ->where('type', 'review')
            ->where('is_approved', true)
            ->get();

        if ($reviews->isEmpty()) {
            return ['count' => 0, 'average' => 0];
        }

        return [
            'count' => $reviews->count(),
            'average' => round($reviews->avg('rating'), 1)
        ];
    }
}
