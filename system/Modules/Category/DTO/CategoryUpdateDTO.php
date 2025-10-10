<?php

namespace Modules\Category\DTO;

use Modules\Shared\DTO\Constructor;

class CategoryUpdateDTO extends Constructor
{
    public int $id;
    public string $name;
    public string|null $description = null;
    public string $url;

    public bool $searchable = true;
    public bool $status = true;
    public bool $isDisplayed;
    public int|null $logo;
    public int|null $banner;
    public $filterPriceMin;
    public $filterPriceMax;

}
