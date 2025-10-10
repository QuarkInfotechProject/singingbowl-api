<?php

namespace Modules\User\DTO;

use Modules\Shared\DTO\Constructor;

class UserChangePasswordDTO extends Constructor
{
    public string $currentPassword;

    public string $newPassword;

    public string $confirmPassword;
}
