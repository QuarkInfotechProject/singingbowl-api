<?php

namespace Modules\AdminUser\DTO;

use Modules\Shared\DTO\Constructor;

class AdminUserFilterDTO extends Constructor
{
    public string|null $name;
    public int|null $status;
    public string|null $email;
}
