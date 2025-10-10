<?php

namespace Modules\OrderProcessing\App\Events;

use Illuminate\Queue\SerializesModels;

class OrderShipped
{
    use SerializesModels;

    public $firstName;
    public $orderId;
    public $mobile;

    /**
     * Create a new event instance.
     */
    public function __construct($firstName, $orderId, $mobile)
    {
        $this->firstName = $firstName;
        $this->orderId = $orderId;
        $this->mobile = $mobile;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
