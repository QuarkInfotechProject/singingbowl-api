<?php

namespace Modules\Order\App\Listeners;

use Modules\Order\App\Events\OrderLogEvent;
use Modules\Order\App\Models\OrderLog;

class OrderLogListener
{
    public function handle(OrderLogEvent $event): void
    {
        $orderLogData = $event->orderLogData;

        try {
            OrderLog::create([
               'description' => $orderLogData['description'],
               'order_id' => $orderLogData['orderId'],
               'modifier_id' => $orderLogData['modifierId'] ?? null
            ]);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
