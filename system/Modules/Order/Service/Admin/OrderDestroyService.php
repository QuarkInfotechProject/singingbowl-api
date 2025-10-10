<?php

namespace Modules\Order\Service\Admin;

use Illuminate\Support\Facades\Log;
use Modules\Order\App\Models\Order;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class OrderDestroyService
{
    function destroy(int $id)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                Log::error('Failed to delete order: Order not found.', [
                    'orderId' => $id
                ]);
                throw new Exception('Order not found.', ErrorCode::NOT_FOUND);
            }

            $order->orderItems()->delete();
            $order->delete();
        } catch (\Exception $exception) {
            Log::error('Failed to delete order: ' . $exception->getMessage(), [
                'exception' => $exception,
                'orderId' => $id
            ]);
            throw $exception;
        }
    }
}
