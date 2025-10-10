<?php

namespace Modules\AdminUser\DTO;

use Modules\Shared\DTO\Constructor;

class AdminUserDeactivateDTO extends Constructor
{
    public string $uuid;
    public string $remarks;
}
