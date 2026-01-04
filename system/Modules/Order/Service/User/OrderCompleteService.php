<?php

namespace Modules\Order\Service\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Order\App\Events\OrderLogEvent;
use Modules\Order\App\Models\Order;
use Modules\Payment\Facades\Gateway;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class OrderCompleteService
{
    function completeOrder($data)
    {
        $userId = null;
        $paymentMethod = null;

        try {
            $paymentMethod = $data['paymentMethod'];

            // Note: This is called from a public callback route (no auth middleware)
            // because GetPay redirects to our callback URL without user session.
            // The orderId is trusted because WE generated the callback URL in GetPayGateway.
            // Payment verification is done via the GetPay token, not user authentication.
            $order = Order::where('id', $data['orderId'])->first();

            if (!$order) {
                Log::error('Order not found', [
                    'order_id' => $data['orderId'],
                ]);
                throw new Exception('Order not found.', ErrorCode::NOT_FOUND);
            }

            // Get user_id from the order itself for logging
            $userId = $order->user_id;

            // Wrap the entire completion process in a database transaction
            // so if anything fails, nothing is committed
            DB::beginTransaction();

            try {
                $gateway = Gateway::get($paymentMethod);
                $response = $gateway->complete($order);

                // Store transaction (this will use its own nested transaction internally)
                $order->storeTransaction($response);

                // Check for Ncell products - wrapped safely to avoid null reference errors
                $ncellProductExists = false;
                try {
                    $ncellProductExists = $order->orderItems
                        ->contains(function ($item) {
                            return $item->product &&
                                   $item->product->categories &&
                                   $item->product->categories->contains(function ($category) {
                                       return $category->name === 'Ncell';
                                   });
                        });
                } catch (\Throwable $e) {
                    Log::warning('Ncell check failed, continuing anyway', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                }

                // Update order status to ORDER_PLACED
                $order->update(['status' => Order::ORDER_PLACED]);

                $this->dispatchOrderStatusChangeEvent(Order::PENDING_PAYMENT, Order::ORDER_PLACED, null, $order->id);

                if ($ncellProductExists) {
                    // $order->update(['status' => Order::NCELL_ORDER]);
                    // $this->dispatchOrderStatusChangeEvent(Order::ORDER_PLACED, Order::NCELL_ORDER, null, $order->id);
                }

                DB::commit();

                Log::info('Order completed successfully', [
                    'order_id' => $order->id,
                    'user_id' => $userId,
                    'payment_method' => $paymentMethod,
                ]);

            } catch (\Throwable $innerException) {
                DB::rollBack();
                throw $innerException;
            }

        } catch (\Throwable $exception) {
            Log::error('Error completing order', [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'user_id' => $userId ?? null,
                'order_id' => $data['orderId'] ?? null,
                'payment_method' => $paymentMethod ?? null,
                'trace' => $exception->getTraceAsString(),
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

