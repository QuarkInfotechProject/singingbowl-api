<?php

namespace Modules\AdminUser\DTO;

use Modules\Shared\DTO\Constructor;

class AdminUserChangePasswordDTO extends Constructor
{
    public string $currentPassword;
    public string $newPassword;
    public string $confirmPassword;
}
