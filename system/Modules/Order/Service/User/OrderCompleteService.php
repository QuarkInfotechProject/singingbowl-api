<?php

namespace Modules\Order\Service\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Order\App\Events\OrderLogEvent;
use Modules\Order\App\Models\Order;
use Modules\Payment\Facades\Gateway;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class OrderCompleteService
{
    function completeOrder($data)
    {
        try {
            $userId = Auth::id();
            $paymentMethod = $data['paymentMethod'];

            $order = Order::where('id', $data['orderId'])
                ->where('user_id', $userId)
                ->first();

            if (!$order) {
                Log::error('Order not found', [
                    'user_id' => $userId,
                    'order_id' => $data['orderId'],
                ]);
                throw new Exception('Order not found.', ErrorCode::NOT_FOUND);
            }

            $gateway = Gateway::get($paymentMethod);

            $response = $gateway->complete($order);

            $order->storeTransaction($response);

            $ncellProductExists = $order->orderItems
                ->contains(function ($item) {
                    return $item->product->categories->contains(function ($category) {
                        return $category->name === 'Ncell';
                    });
                });

            $order->update(['status' => Order::ORDER_PLACED]);

            $this->dispatchOrderStatusChangeEvent(Order::PENDING_PAYMENT, Order::ORDER_PLACED, $modifierId ?? null, $order->id);

            if ($ncellProductExists) {
                // $order->update(['status' => Order::NCELL_ORDER]);
                // $this->dispatchOrderStatusChangeEvent(Order::ORDER_PLACED, Order::NCELL_ORDER, $modifierId ?? null, $order->id);
            }

            Log::info('Order completed successfully', [
                'order_id' => $order->id,
                'user_id' => $userId,
                'payment_method' => $paymentMethod,
            ]);
        } catch (Exception $exception) {
            Log::error('Error completing order', [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'user_id' => $userId ?? null,
                'order_id' => $data['orderId'] ?? null,
                'payment_method' => $paymentMethod ?? null,
            ]);
            throw $exception;
        }
    }

    private function dispatchOrderStatusChangeEvent(string $fromStatus, string $toStatus, ?string $modifierId, $orderId)
    {
        Event::dispatch(new OrderLogEvent(
            "Order status changed from " . Order::$orderStatusMapping[$fromStatus] .
            " to " . Order::$orderStatusMapping[$toStatus] . ".",
            $orderId,
            $modifierId
        ));
    }
}
