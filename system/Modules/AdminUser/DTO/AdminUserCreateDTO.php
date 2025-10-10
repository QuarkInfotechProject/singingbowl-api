<?php

namespace Modules\AdminUser\DTO;

use Modules\Shared\DTO\Constructor;

class AdminUserCreateDTO extends Constructor
{
    public string $name;
    public string $email;
    public string $password;
    public int $groupId;
}
