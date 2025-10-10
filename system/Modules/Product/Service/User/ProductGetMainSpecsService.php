<?php

namespace Modules\Product\Service\User;

use Modules\Product\Trait\ValidateProductTrait;

class ProductGetMainSpecsService
{
    use ValidateProductTrait;

    function show(string $url)
    {
        $product = $this->validateProduct($url);

        return $product->specifications;

//        if (!is_array($specifications)) {
//            return [];
//        }
//
//        return array_map(function($spec) {
//            return [
//                'file' => url('/modules/productSpecificationIcons/' . $spec['file']) ?? '',
//                'label' => $spec['label'] ?? '',
//            ];
//        }, $specifications);
    }
}
