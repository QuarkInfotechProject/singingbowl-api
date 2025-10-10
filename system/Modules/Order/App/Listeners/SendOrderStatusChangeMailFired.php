<?php

namespace Modules\Order\App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Order\App\Emails\OrderStatusUpdateMail;
use Modules\Order\App\Events\SendOrderStatusChangeMail;

class SendOrderStatusChangeMailFired implements ShouldQueue
{
    public function handle(SendOrderStatusChangeMail $event): void
    {
        $order = $event->order;
        $email = $order->user->email;

        try {
            $mailData = [
                'title' => $event->sendOrderNoteDTO->title,
                'subject' => $event->sendOrderNoteDTO->subject,
                'message' => $event->sendOrderNoteDTO->message,
                'description' => $event->sendOrderNoteDTO->description,
            ];
            
            Mail::to($email)->send(new OrderStatusUpdateMail($mailData));
        } catch (\Exception $exception) {
            // Log the exception or handle it as per application's error handling policy
            // For now, re-throwing to ensure it's caught by the queue worker
            throw $exception;
        }
    }
}
