<?php

namespace Modules\Order\App\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Order\DTO\SendOrderInvoiceEmailDTO;

class SendOrderInvoiceMail
{
    use SerializesModels;

    /**
     * Order data to be included in the invoice
     * @var array
     */
    public $orderData;

    /**
     * Create a new event instance.
     */
    public function __construct(
        array $orderData,
        public SendOrderInvoiceEmailDTO $sendOrderInvoiceEmailDTO
    ) {
        $this->orderData = $orderData;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
