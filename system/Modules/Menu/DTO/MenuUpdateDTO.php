<?php

namespace Modules\Menu\DTO;

use Modules\Shared\DTO\Constructor;

class MenuUpdateDTO extends Constructor
{
    public int $id;
    public string $title;
    public string|null $url;
    public string|null $icon;
}
