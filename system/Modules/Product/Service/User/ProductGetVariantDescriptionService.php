<?php

namespace Modules\Product\Service\User;
use Carbon\Carbon;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductOptionValue;
use Modules\Product\App\Models\ProductVariant;
use Modules\Product\App\Models\ProductOption;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\Shared\Services\CacheService;

class ProductGetVariantDescriptionService
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    function show(string $productSlug)
    {
        $product = Product::where('slug', $productSlug)->select('id')->first();

        if (!$product) {
            throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
        }

        $productId = $product->id;
        $cacheKey = $this->cacheService->generateProductVariantDescriptionKey($productId);
        $cacheTags = ['product:' . $productId];

        $cachedData = $this->cacheService->get($cacheKey, $cacheTags);

        if ($cachedData) {
            return $cachedData;
        }

        try {
            $variants = ProductVariant::where('product_id', $product->id)
                ->select(
                    'id', 'uuid', 'product_id', 'name as productName',
                    'original_price as originalPrice', 'special_price as specialPrice',
                    'special_price_start as specialPriceStart',
                    'special_price_end as specialPriceEnd',
                    'quantity', 'sku', 'in_stock',
                )
                ->get();

            $formattedVariants = $variants->map(function($variant) {
                $currentDate = now();
                $isSpecialPriceValid = false;

                if ($variant->specialPrice !== null) {
                    $specialPriceStart = $variant->specialPriceStart ? Carbon::parse($variant->specialPriceStart) : null;
                    $specialPriceEnd = $variant->specialPriceEnd ? Carbon::parse($variant->specialPriceEnd) : null;
                    $isSpecialPriceValid =
                        (!$specialPriceStart || $currentDate->gte($specialPriceStart)) &&
                        (!$specialPriceEnd || $currentDate->lte($specialPriceEnd));
                }

                $activeSpecialPrice = $isSpecialPriceValid ? $variant->specialPrice : null;

                return [
                    'uuid' => $variant->uuid,
                    'productName' => $variant->productName,
                    'originalPrice' => (string)$variant->originalPrice,
                    'specialPrice' => $activeSpecialPrice !== null ? (string)$activeSpecialPrice : null,
                    'specialPriceStart' => $variant->specialPriceStart,
                    'specialPriceEnd' => $variant->specialPriceEnd,
                    'quantity' => (string)$variant->quantity,
                    'sku' => $variant->sku,
                    'inStock' => $variant->in_stock,
                    'discountPercentage' => $activeSpecialPrice !== null ? round((($variant->originalPrice - $activeSpecialPrice) / $variant->originalPrice) * 100, 2) : 0,
                ];
            })->toArray();

            $variations = $this->getProductVariations($product->id);

            $result = [
                'data' => [
                    [
                        'variants' => $formattedVariants,
                        'variations' => $variations
                    ]
                ]
            ];

            // Cache the result
            $this->cacheService->put($cacheKey, $result, $this->cacheService->getProductDetailTtl(), $cacheTags);

            return $result;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    private function getProductVariations($productId)
    {
        // Create a specific cache key for variations
        $variationCacheKey = $this->cacheService->generateProductVariantDescriptionKey($productId) . ':variations';
        $cacheTags = ['product:' . $productId];

        $cachedVariations = $this->cacheService->get($variationCacheKey, $cacheTags);

        if ($cachedVariations !== null) {
            return $cachedVariations;
        }

        $variations = [];

        $options = ProductOption::where('product_id', $productId)
            ->select('id', 'name')
            ->get();

        foreach ($options as $option) {
            $optionValues = ProductOptionValue::where('product_option_id', $option->id)
                ->select('option_name')
                ->distinct()
                ->pluck('option_name')
                ->toArray();

            $variations[] = [
                'variation_name' => $option->name,
                'values' => $optionValues,
            ];
        }

        // Cache the variations
        $this->cacheService->put($variationCacheKey, $variations, $this->cacheService->getProductDetailTtl(), $cacheTags);

        return $variations;
    }
}
