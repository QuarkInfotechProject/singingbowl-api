<?php

namespace Modules\Product\DTO;

use Modules\Shared\DTO\Constructor;

class ProductVariantsDTO extends Constructor
{
    public string $name;
    public string $sku;
    public bool $status = true;
    public int $originalPrice;

    public int|null $specialPrice;
    public string|null $specialPriceStart;
    public string|null $specialPriceEnd;

    public int $quantity;
    public bool $inStock = true;
    public $optionValues = [];
}
