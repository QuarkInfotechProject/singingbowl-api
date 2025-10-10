<?php

namespace Modules\Menu\DTO;

use Modules\Shared\DTO\Constructor;

class MenuCreateDTO extends Constructor
{
    public string $title;
    public int|null $parentId;
    public string|null $url;
    public string|null $icon;
}
