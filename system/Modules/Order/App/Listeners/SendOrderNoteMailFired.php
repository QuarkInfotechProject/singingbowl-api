<?php

namespace Modules\Order\App\Listeners;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Modules\Order\App\Emails\OrderInvoiceMail;
use Modules\Order\App\Events\SendOrderNoteMail;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class SendOrderNoteMailFired
{
    public function handle(SendOrderNoteMail $event): void
    {
        $order = $event->order;
        $email = $order->user->email;
        $orderId = $order->id;

        try {
            $fileName = "invoice_{$orderId}.pdf";
            $pdfPath = public_path('modules/order/invoices/' . $fileName);

            if (!File::exists($pdfPath)) {
                throw new Exception("Invoice not found for orderId: {$orderId}", ErrorCode::NOT_FOUND);
            }

            $sendOrderNoteMailDTO = $event->sendOrderNoteDTO;
            Mail::to('the_game_cena@yahoo.com')->send(new OrderInvoiceMail($pdfPath, $sendOrderNoteMailDTO));
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
