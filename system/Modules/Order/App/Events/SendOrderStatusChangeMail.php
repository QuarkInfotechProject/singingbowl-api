<?php

namespace Modules\Order\App\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Order\DTO\SendOrderInvoiceEmailDTO;
use Modules\Order\DTO\SendOrderNoteDTO;

class SendOrderStatusChangeMail
{
    use SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct($order, public SendOrderNoteDTO $sendOrderNoteDTO)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
