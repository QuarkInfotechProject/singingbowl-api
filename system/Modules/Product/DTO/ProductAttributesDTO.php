<?php

namespace Modules\Product\DTO;

use Modules\Shared\DTO\Constructor;

class ProductAttributesDTO extends Constructor
{
    public int|null $attributeId;
    public int|null $values;
}
