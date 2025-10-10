<?php

namespace Modules\Product\Trait;

use Modules\Product\App\Models\Product;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

trait ValidateProductTrait
{
    function validateProduct(string $url)
    {
        $product = Product::where('slug', $url)->first();

        if (!$product) {
            throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
        }

        return $product;
    }
}
