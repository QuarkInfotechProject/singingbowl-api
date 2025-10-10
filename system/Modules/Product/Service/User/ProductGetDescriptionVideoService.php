<?php

namespace Modules\Product\Service\User;

use Modules\Product\Trait\ValidateProductTrait;

class ProductGetDescriptionVideoService
{
    use ValidateProductTrait;

    function show(string $url)
    {
        $product = $this->validateProduct($url);

        $descriptionVideo = $product->filterFiles('descriptionVideo')->first();

        if ($descriptionVideo) {
            return $descriptionVideo->url;
        }

        return null;
    }
}
