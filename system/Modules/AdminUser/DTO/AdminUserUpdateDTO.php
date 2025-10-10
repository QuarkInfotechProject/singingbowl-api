<?php

namespace Modules\AdminUser\DTO;

use Modules\Shared\DTO\Constructor;

class AdminUserUpdateDTO extends Constructor
{
    public string $uuid;
    public string $name;
    public string|null $password;
    public int|null $groupId;
}
