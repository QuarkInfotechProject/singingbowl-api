<?php

namespace Modules\Product\DTO;

use Modules\Shared\DTO\Constructor;

class ProductFilterDTO extends Constructor
{
    public string|null $name;
    public int $status;
    public string|null $sku;
    public string|null $sortBy;
    public string|null $sortDirection;
    public int|null $per_page;
}
