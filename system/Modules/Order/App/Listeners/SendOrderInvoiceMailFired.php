<?php

namespace Modules\Order\App\Listeners;

use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Modules\Order\App\Emails\OrderInvoiceMail;
use Modules\Order\App\Events\SendOrderInvoiceMail;

class SendOrderInvoiceMailFired implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2; // 1 retry (original attempt + 1 retry)

    /**
     * Handle the event.
     */
    public function handle(SendOrderInvoiceMail $event): void
    {
        $orderData = $event->orderData;

        // Get customer email from addressInformation
        $customerEmail = $orderData['addressInformation']['email'] ?? null;

        if (!$customerEmail) {
            return;
        }

        try {
            // Create directory if it doesn't exist
            $directory = public_path('modules/order/invoices/');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Generate PDF
            $pdf = PDF::loadView('order::invoices_pdf', compact('orderData'));
            $fileName = "invoice_{$orderData['id']}.pdf";
            $filePath = $directory . $fileName;
            // Delete existing file if present to avoid FileAlreadyExistsException
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $pdf->save($filePath);

            // Send email with PDF attachment
            $emailDTO = $event->sendOrderInvoiceEmailDTO;

            Mail::to($customerEmail)->send(new OrderInvoiceMail($filePath, $emailDTO));

        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
