<?php

namespace Modules\Product\Service\User;

use Modules\Product\App\Models\Product;
use Modules\Product\Trait\GetBasicProductInformationTrait;

class ProductIndexWithVideoService
{
    use GetBasicProductInformationTrait;

    function index()
    {
        $query = Product::active();
        $products = $query->latest()->get();

        $filteredProducts = $products->filter(function ($product) {
            return $product->filterFiles('descriptionVideo')->count() > 0;
        })->values();

        $filteredProducts->load([
            'variants' => function ($query) {
                $query->with(['optionValues.files' => function ($q) {
                    $q->where('zone', 'baseImage');
                }]);
            }
        ]);

        return $filteredProducts->map(function ($product) {
            $productVariant = $product->has_variant
                ? $product->variants->first()
                : null;

            $baseImage = $this->getOptimizedBaseImage($product, $productVariant);
            $descriptionVideo = $this->getDescriptionVideoFromProduct($product);

            return [
                'id' => $product->uuid,
                'productName' => $product->product_name,
                'url' => $product->slug,
                'originalPrice' => $product->original_price ?? $productVariant?->original_price,
                'specialPrice' => $this->validateSpecialPrice($product, $productVariant),
                'priceDifferencePercentage' => $this->calculatePriceDifferencePercentage($product, $productVariant),
                'baseImage' => $baseImage,
                'descriptionVideo' => $descriptionVideo,
            ];
        })->values()->all();
    }

    private function getDescriptionVideoFromProduct($product)
    {
        $file = $product->filterFiles('descriptionVideo')->first();
        return $file ? $file->url : null;
    }
}
