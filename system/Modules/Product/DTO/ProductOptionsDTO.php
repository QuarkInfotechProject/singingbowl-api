<?php

namespace Modules\Product\DTO;

use Modules\Shared\DTO\Constructor;

class ProductOptionsDTO extends Constructor
{
    public string $name;
    public bool $hasImage = false;

    /**
     * @var ProductOptionValuesDTO[]
     */
    public $values;
}
