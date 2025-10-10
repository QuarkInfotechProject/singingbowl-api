<?php

namespace Modules\Order\Service\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Order\App\Events\OrderLogEvent;
use Modules\Order\App\Events\SendOrderStatusChangeMail;
use Modules\Order\App\Models\Order;
use Modules\Order\DTO\SendOrderNoteDTO;
use Modules\Order\Trait\RestoreStock;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductVariant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\SystemConfiguration\App\Models\EmailTemplate;

class OrderCancelService
{
    use RestoreStock;

    function cancelOrder(int $orderId)
    {
        $order = Order::with('user')->find($orderId);

        if (!$order) {
            $message = 'Order not found.';
            Log::error($message, ['orderId' => $orderId]);
            throw new Exception($message, ErrorCode::NOT_FOUND);
        }

        if ($order->status === Order::CANCELLED) {
            $message = 'Order is already in cancelled state.';
            Log::warning($message, ['orderId' => $orderId]);
            throw new Exception($message, ErrorCode::CONFLICT);
        }

        if (!in_array($order->status, [Order::ORDER_PLACED, Order::PENDING_PAYMENT])) {
            throw new Exception('Order is already being processed. Please contact support.', ErrorCode::UNPROCESSABLE_CONTENT);
        }

        $fromStatus = $order->status;

        Event::dispatch(
            new OrderLogEvent(
                "Order cancelled by customer. Order status changed from " . Order::$orderStatusMapping[$fromStatus] . " to "
                . Order::$orderStatusMapping[Order::CANCELLED] . ".",
                $order->id,
                null,
            )
        );

        try {
            DB::beginTransaction();

            $this->restoreStock($order);

            $order->update([
                'cancelled_date' => now(),
                'status' => Order::CANCELLED
            ]);

            $this->sendOrderStatusChangeMail($order);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Order cancellation failed: ' . $exception->getMessage(), [
                'orderId' => $orderId,
                'exception' => $exception
            ]);
            DB::rollBack();
            throw $exception;
        }
    }

    private function sendOrderStatusChangeMail(Order $order)
    {
        $newStatus = Order::CANCELLED;
        $templateName = 'order_status_' . strtolower($newStatus);
        $template = EmailTemplate::where('name', $templateName)->first();

        if (!$template) {
            Log::error('Email template not found.', ['templateName' => $templateName]);
            return;
        }

        $message = strtr($template->message, [
            '{FULLNAME}' => $order->user->full_name,
            '{ORDERID}' => $order->id,
            '{STATUS}' => Order::$orderStatusMapping[$newStatus],
        ]);

        $description = strtr($template->description, [
            '{FULLNAME}' => $order->user->full_name,
            '{ORDERID}' => $order->id,
            '{STATUS}' => Order::$orderStatusMapping[$newStatus],
        ]);

        $mailData = SendOrderNoteDTO::from([
            'title' => strtr($template->title, [
                '{ORDERID}' => $order->id,
            ]),
            'subject' => strtr($template->subject, [
                '{ORDERID}' => $order->id,
            ]),
            'message' => $message,
            'description' => $description,
        ]);

        Event::dispatch(new SendOrderStatusChangeMail($order, $mailData));
    }
}
