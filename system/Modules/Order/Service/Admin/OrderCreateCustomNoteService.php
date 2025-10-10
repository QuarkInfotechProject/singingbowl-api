<?php

namespace Modules\Order\Service\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Order\App\Events\SendOrderNoteMail;
use Modules\Order\App\Models\Order;
use Modules\Order\App\Models\OrderAddress;
use Modules\Order\App\Models\OrderLog;
use Modules\Order\DTO\SendOrderNoteDTO;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\SystemConfiguration\App\Models\EmailTemplate;

class OrderCreateCustomNoteService
{
    function create($data)
    {
        $userId = Auth::id();

        $order = Order::with(['user', 'orderLog'])->find($data['orderId']);

        if (!$order) {
            throw new Exception('Order not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $orderNote = OrderLog::create([
                'description' => $data['note'],
                'order_id' => $order->id,
                'modifier_id' => $userId,
                'note_type' => $data['noteType'],
            ]);

            if ($data['noteType'] == OrderLog::CUSTOMER_NOTE) {
                $this->sendOrderNote($order->user_id, $order, $orderNote);
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    private function sendOrderNote($userId, $order, $orderNote)
    {
        $orderAddress = OrderAddress::where('user_id', $userId)
            ->where('order_id', $order->id)
            ->with('address')
            ->first();

        if (!$orderAddress) {
            throw new Exception('Address not found for related order.', ErrorCode::NOT_FOUND);
        }

        $template = EmailTemplate::where('name', 'customer_note_added')->first();

        if (!$template) {
            Log::error('Email template not found.', ['templateName' => 'customer_note_added']);
            throw new Exception('Email template not found.', ErrorCode::NOT_FOUND);
        }

        $title = strtr($template->title, [
            '{DATE}' => $orderNote->created_at->format('l jS \o\f F Y, h:ia')
        ]);

        $message = strtr($template->message, [
            '{FULLNAME}' => $orderAddress->address->first_name . ' ' . $orderAddress->address->last_name,
            '{NOTE}' => $orderNote->description
        ]);

        $description = strtr($template->description, [
            '{ORDERID}' => $order->id,
            '{NOTE}' => $orderNote->description
        ]);

        $sendOrderNoteDTO = SendOrderNoteDTO::from([
            'title' => $title,
            'subject' => $template->subject,
            'message' => $message,
            'description' => $description,
        ]);

        Event::Dispatch(new SendOrderNoteMail($order, $sendOrderNoteDTO));
    }
}
