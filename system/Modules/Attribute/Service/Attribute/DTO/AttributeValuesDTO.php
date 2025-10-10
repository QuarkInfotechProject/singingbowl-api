<?php

namespace Modules\Attribute\Service\Attribute\DTO;

use Modules\Shared\DTO\Constructor;

class AttributeValuesDTO extends Constructor
{
    public int|null $id;
    public string|null $value;
}
