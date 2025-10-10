<?php

namespace Modules\Order\App\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject($this->mailData['subject'])
            ->view('order::order_status_update', [
                'mailData' => $this->mailData
            ]);
    }
}