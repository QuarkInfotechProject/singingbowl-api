<?php

namespace Modules\Order\Service\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Order\App\Events\OrderLogEvent;
use Modules\Order\App\Events\SendOrderNoteMail;
use Modules\Order\App\Events\SendOrderStatusChangeMail;
use Modules\Order\App\Models\Order;
use Modules\Order\App\Models\OrderAddress;
use Modules\Order\DTO\SendOrderNoteDTO;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\SystemConfiguration\App\Models\EmailTemplate;

class OrderChangeStatusService
{
    function changeOrderStatus($data)
    {
        try {
            $modifierId = Auth::id();
            $order = Order::find($data['orderId']);

            if (!$order) {
                Log::error('Failed to change order status: Order not found.', [
                    'orderId' => $data['orderId']
                ]);
                throw new Exception('Order not found.', ErrorCode::NOT_FOUND);
            }

            Event::dispatch(
                new OrderLogEvent(
                    "Order status changed from " . Order::$orderStatusMapping[$order->status] .
                    " to " . Order::$orderStatusMapping[$data['status']] . ".",
                    $order->id,
                    $modifierId ?? null,
                )
            );

            $this->sendOrderStatusChange($order->user_id, $order, $data['status']);

            $order->update(['status' => $data['status']]);
        } catch (\Exception $exception) {
            Log::error('Failed to change order status: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data
            ]);
            throw $exception;
        }
    }

    private function sendOrderStatusChange($userId, $order, $status)
    {
        $orderAddress = OrderAddress::where('user_id', $userId)
            ->where('order_id', $order->id)
            ->with('address')
            ->first();

        if (!$orderAddress) {
            throw new Exception('Address not found for related order.', ErrorCode::NOT_FOUND);
        }

        $templateName = 'order_status_' . strtolower($status);
        $template = EmailTemplate::where('name', $templateName)->first();

        if (!$template) {
            Log::error('Email template not found.', ['templateName' => $templateName]);
            throw new Exception('Email template not found.', ErrorCode::NOT_FOUND);
        }

        $message = strtr($template->message, [
            '{FULLNAME}' => $order->user->full_name,
            '{ORDERID}' => $order->id,
            '{STATUS}' => Order::$orderStatusMapping[$status],
            '{REFUND_AMOUNT}' => number_format($order->total_refunded, 2),
        ]);

        $description = strtr($template->description, [
            '{FULLNAME}' => $order->user->full_name,
            '{ORDERID}' => $order->id,
            '{STATUS}' => Order::$orderStatusMapping[$status],
            '{REFUND_AMOUNT}' => number_format($order->total_refunded, 2),
        ]);

        $mailData = \Modules\Order\DTO\SendOrderNoteDTO::from([
            'title' => strtr($template->title, [
                '{ORDERID}' => $order->id,
            ]),
            'subject' => strtr($template->subject, [
                '{ORDERID}' => $order->id,
            ]),
            'message' => $message,
            'description' => $description,
        ]);

        Event::Dispatch(new SendOrderStatusChangeMail($order, $mailData));
    }
}
