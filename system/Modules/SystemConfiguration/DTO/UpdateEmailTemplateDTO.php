<?php

namespace Modules\SystemConfiguration\DTO;

use Modules\Shared\DTO\Constructor;

class UpdateEmailTemplateDTO extends Constructor
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $subject;

    /**
     * @var string
     */
    public string $message;
}
