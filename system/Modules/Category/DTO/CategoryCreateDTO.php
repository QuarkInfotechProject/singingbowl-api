<?php

namespace Modules\Category\DTO;

use Modules\Shared\DTO\Constructor;

class CategoryCreateDTO extends Constructor
{
    public string $name;
    public string|null $description = null;
    public string $url;
    public bool $searchable = true;
    public bool $status = true;
    public int|null $logo;
    public int|null $banner;
    public int $parentId;
    public $filterPriceMin;
    public $filterPriceMax;

}
