<?php

namespace Modules\Attribute\Service\Attribute\DTO;

use Modules\Shared\DTO\Constructor;

class AttributeCreateDTO extends Constructor
{
    public int $attributeSetId;
    public string $name;
    public bool|null $is_enabled = true;
    public int $sort_order=0;
    public array|null $category_ids;


    /**
     * @var AttributeValuesDTO[]
     */
    public $values;
}
