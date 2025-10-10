<?php

namespace Modules\Product\DTO;

use Modules\Shared\DTO\Constructor;

class ProductUpdateVariantDTO extends Constructor
{
    public string $productUuid;
    public string $variantUuid;
    public bool $status;
    public int $originalPrice;
    public int|null $specialPrice;
    public string|null $specialPriceStart;
    public string|null $specialPriceEnd;
    public int $quantity;
    public bool $inStock;
}
