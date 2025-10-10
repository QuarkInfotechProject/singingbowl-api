<?php

namespace Modules\Order\Service\Admin;

use Modules\Order\App\Models\Order;
use Modules\Order\App\Models\OrderLog;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class OrderShowLogService
{
    function show(int $orderId)
    {
        $order = Order::with(['orderLog'])
            ->find($orderId);

        if (!$order) {
            throw new Exception('Order not found.', ErrorCode::NOT_FOUND);
        }

        $orderLogs = $order->orderLog()->orderBy('id', 'desc')->get();

        $logs = [];

        foreach ($orderLogs as $orderLog) {
            $logs[] = [
                'noteId' => $orderLog->id,
                'noteType' => OrderLog::$noteType[$orderLog->note_type],
                'description' => $orderLog['description'],
                'createdAt' => $orderLog->created_at->format('Y-m-d \a\t h:i A'),
                'modifierName' => $orderLog->modifier_id ? $orderLog->modifier->name : null,
            ];
        }

        return $logs;
    }
}
