<?php

namespace Modules\Product\Trait;

use Carbon\Carbon;

trait GetBasicProductInformationTrait
{
    function getBasicProductInformation($products)
    {

        $products->load([
            'reviews' => function ($query) {
                $query->where('type', 'review')
                    ->where('is_approved', true);
            },
            'options' => function ($query) {
                $query->where('name', 'Color');
            },
            'variants' => function ($query) {
                $query->with(['optionValues.files' => function ($q) {
                    $q->whereIn('zone', ['baseImage', 'additionalImage']);
                }]);
            },
            'brand'
        ]);

        return $products->map(function ($product) {
            $productVariant = $product->has_variant
                ? $product->variants->first()
                : null;

            $baseImage = $this->getOptimizedBaseImage($product, $productVariant);

            $productReviews = $product->reviews;
            $reviewCount = $productReviews->count();
            $rating = round($productReviews->avg('rating'));

            return [
                'id' => $product->uuid,
                'productName' => $product->product_name,
                'url' => $product->slug,
                'brandId'=>$product->brand_id,
                'bestSeller' => (bool)$product->best_seller,
                'isNew' => $product->is_new,
                'onSale' => $product->on_sale,
                'soldCount' => $product->total_completed_sold,
                'inStock' => $product->in_stock ?? $productVariant?->in_stock,
                'originalPrice' => $product->original_price ?? $productVariant?->original_price,
                'specialPrice' => $this->validateSpecialPrice($product, $productVariant),
                'priceDifferencePercentage' => $this->calculatePriceDifferencePercentage($product, $productVariant),
                'reviewCount' => $reviewCount,
                'rating' => $rating,
                'baseImage' => $baseImage,
                'productOption' => $this->getOptimizedProductOptions($product)
            ];
        })->toArray();
    }

    private function getOptimizedBaseImage($product, $productVariant)
    {
        if ($productVariant && $productVariant->optionValues->isNotEmpty()) {
            return $productVariant->optionValues
                ->first()
                ->filterFiles('baseImage')
                ->first()?->url;
        }

        return $product
            ->filterFiles('baseImage')
            ->first()?->url;
    }

    private function calculatePriceDifferencePercentage($product, $productVariant)
    {
        $originalPrice = $product->original_price ?? $productVariant?->original_price;
        $specialPrice = $this->validateSpecialPrice($product, $productVariant);

        if ($specialPrice && $originalPrice && $originalPrice > $specialPrice) {
            return round(($originalPrice - $specialPrice) / $originalPrice * 100);
        }

        return 0;
    }

    private function validateSpecialPrice($product, $productVariant)
    {
        $now = Carbon::now();
        $specialPrice = $product->special_price ?? $productVariant?->special_price;
        $specialPriceStart = $product->special_price_start ?? $productVariant?->special_price_start;
        $specialPriceEnd = $product->special_price_end ?? $productVariant?->special_price_end;

        if (
            $specialPrice > 0 &&
            (!$specialPriceStart || $now->gte($specialPriceStart)) &&
            (!$specialPriceEnd || $now->lte($specialPriceEnd))
        ) {
            return $specialPrice;
        }

        return '';
    }

    private function getOptimizedProductOptions($product)
    {
        $colorOption = $product->options
            ->first();

        if (!$colorOption) {
            return null;
        }

        $valuesData = $colorOption->values->map(function ($value) {
            $baseImage = $value->additionalImages()->first();

            return [
                'id' => $value->uuid,
                'optionName' => $value->option_name,
                'optionData' => $value->option_data ?? '',
                'baseImage' => $baseImage?->url ?? '',
            ];
        });

        return [
            'optionId' => $colorOption->uuid,
            'optionName' => $colorOption->name,
            'values' => $valuesData
        ];
    }
}
