<?php

namespace Modules\User\DTO;

use Modules\Shared\DTO\Constructor;

class UserForgotPasswordDTO extends Constructor
{
    public string $title;
    public string $subject;
    public string $description;
    public string|null $name = 'Quark Ecommerce';
    public string $email;
    public string $token;
}
