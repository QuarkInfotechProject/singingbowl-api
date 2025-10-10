<?php

namespace Modules\OrderProcessing\App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Order\App\Emails\OrderInvoiceMail;

class SendOrderEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $email;
    protected $pdfPath;
    protected $sendOrderNoteDTO;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $pdfPath, $sendOrderNoteDTO)
    {
        $this->email = $email;
        $this->pdfPath = $pdfPath;
        $this->sendOrderNoteDTO = $sendOrderNoteDTO;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->email)
                ->send(new OrderInvoiceMail($this->pdfPath, $this->sendOrderNoteDTO));

            Log::info("Email sent successfully to {$this->email}");
        } catch (\Exception $exception) {
            Log::error("Failed to send email to {$this->email}: " . $exception->getMessage());
        }
    }
}
