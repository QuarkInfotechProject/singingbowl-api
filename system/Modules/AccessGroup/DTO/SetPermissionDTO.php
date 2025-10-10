<?php

namespace Modules\AccessGroup\DTO;

use Modules\Shared\DTO\Constructor;

class SetPermissionDTO extends Constructor
{
    public string $name;
    public string|null $description;
    public string|null $section;
    public bool $isIndex;
}
