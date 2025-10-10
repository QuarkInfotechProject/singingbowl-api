<?php

namespace Modules\Order\App\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Modules\Order\DTO\SendOrderInvoiceEmailDTO;

class OrderInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string Path to the invoice PDF file
     */
    protected $pdfPath;

    /**
     * @var SendOrderInvoiceEmailDTO Email template data
     */
    protected $sendOrderInvoiceEmailDTO;

    /**
     * Create a new message instance.
     */
    public function __construct(string $pdfPath, SendOrderInvoiceEmailDTO $sendOrderInvoiceEmailDTO)
    {
        $this->pdfPath = $pdfPath;
        $this->sendOrderInvoiceEmailDTO = $sendOrderInvoiceEmailDTO;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject($this->sendOrderInvoiceEmailDTO->subject)
            ->view('order::invoice', [
                'orderInvoice' => $this->sendOrderInvoiceEmailDTO
            ]);
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('Invoice.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
