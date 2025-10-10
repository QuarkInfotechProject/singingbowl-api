<?php

namespace Modules\Order\DTO;

use Modules\Shared\DTO\Constructor;

class SendOrderInvoiceEmailDTO extends Constructor
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
    public string $message;

    /**
     * @var string
     */
    public string $description;

    /**
     * @var string|null
     */
    public string|null $image;
}
