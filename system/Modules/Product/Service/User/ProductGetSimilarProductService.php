<?php

namespace Modules\Product\Service\User;

use Modules\Product\Trait\GetBasicProductInformationTrait;
use Modules\Product\Trait\ValidateProductTrait;

class ProductGetSimilarProductService
{
    use GetBasicProductInformationTrait, ValidateProductTrait;

    function show(string $url)
    {
        $product = $this->validateProduct($url);

        $productData = [];

        $upSellProducts = $this->getUpSellProducts($product);
        $crossSellProducts = $this->getCrossSellProducts($product);
        $relatedProducts = $this->getRelatedProducts($product);

        $productData[] = $this->formatProductData($upSellProducts, $crossSellProducts, $relatedProducts);

        return $productData;
    }

    private function getUpSellProducts($product)
    {
        $upSellProducts = $product->upSellProducts()->get();

        return $this->getBasicProductInformation($upSellProducts);
    }

    private function getCrossSellProducts($product)
    {
        $crossSellProducts = $product->crossSellProducts()->get();

        return $this->getBasicProductInformation($crossSellProducts);
    }

    private function getRelatedProducts($product)
    {
        $relatedProducts = $product->relatedProducts()->get();

        return $this->getBasicProductInformation($relatedProducts);
    }

    private function formatProductData($upSellProducts, $crossSellProducts, $relatedProducts)
    {
        return [
            'upSellProducts' => $upSellProducts,
            'crossSellProducts' => $crossSellProducts,
            'relatedProducts' => $relatedProducts
        ];
    }
}
