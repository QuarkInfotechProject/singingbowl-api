<?php

namespace Modules\Order\DTO;

use Modules\Shared\DTO\Constructor;

class SendOrderNoteDTO extends Constructor
{
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
    public string $message;

    /**
     * @var string
     */
    public string $description;
}
