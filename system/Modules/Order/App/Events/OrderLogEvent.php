<?php

namespace Modules\Order\App\Events;

use Illuminate\Queue\SerializesModels;

class OrderLogEvent
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(string $description, string $orderId, ?string $modifierId)
    {
        $this->orderLogData = [
            'description' => $description,
            'orderId' => $orderId,
            'modifierId' => $modifierId
        ];
    }
}
