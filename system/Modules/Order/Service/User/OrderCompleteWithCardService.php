<?php

namespace Modules\Order\Service\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Modules\Order\App\Events\OrderLogEvent;
use Modules\Order\App\Models\Order;
use Modules\Payment\Facades\Gateway;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class OrderCompleteWithCardService
{
    function completeOrderWithCard($data)
    {
        $referenceNumber = $data['req_reference_number'];
        $orderId = explode('|', $referenceNumber)[0];

        $paymentMethod = $data['req_payment_method'];

        $order = Order::where('id', $orderId)
            ->first();

        if (!$order) {
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

        return $orderId;
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
