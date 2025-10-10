<?php

namespace Modules\User\DTO;

use Modules\Shared\DTO\Constructor;
class SendRegisterEmailDTO extends Constructor
{
    /**
     * @var string|null
     */
    public string|null $system;

    /**
     * @var string
     */
    public string $title;

    /**
     * @var string
     */
    public string $subject;

    /**
     * @var string
     */
    public string $email;

    /**
     * @var string
     */
    public string $description;

    /**
     * @var string|null
     */
    public string|null $image;

    /**
     * @var int|null
     */
    public int|null $code;
}
