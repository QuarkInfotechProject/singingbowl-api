<?php

namespace Modules\OrderProcessing\App\Jobs;

use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Modules\Order\App\Models\OrderAddress;
use Modules\Order\DTO\SendOrderNoteDTO;
use Modules\OrderProcessing\Service\CreateOrderArtifactsService;
use Modules\Order\App\Models\Order;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\SystemConfiguration\App\Models\EmailTemplate;

class CreateOrderArtifactsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(CreateOrderArtifactsService $service)
    {
        try {
            $this->sendOrderStatusEmails();
            $service->processOrders($this->data);
        } catch (\Exception $exception) {
            Log::error('Error processing order artifacts: ' . $exception->getMessage());
            throw $exception;
        }
    }

    /**
     * Send email notifications to users about order status change.
     */
    private function sendOrderStatusEmails()
    {
        $orders = Order::whereIn('id', $this->data['orders'])
            ->with('user')
            ->get();

        foreach ($orders as $order) {
            if ($order->user && $order->user->email) {
                $fileName = "invoice_{$order->id}.pdf";
                $pdfPath = public_path('modules/order/invoices/' . $fileName);

                if (!File::exists($pdfPath)) {
                    throw new Exception("Invoice not found for orderId: {$order->id}", ErrorCode::NOT_FOUND);
                }

                $sendOrderNoteDTO = $this->sendOrderStatusChange($order->user->id, $order);

                $emailJobs[] = new SendOrderEmailJob($order->user->email, $pdfPath, $sendOrderNoteDTO);
            }
        }

        Bus::batch($emailJobs)
            ->then(function (Batch $batch) {
                Log::info('All emails sent successfully.');
            })
            ->catch(function (Batch $batch, \Exception $exception) {
                Log::error('Failed to send some emails: ' . $exception->getMessage());
            })
            ->finally(function (Batch $batch) {
                Log::info('Email sending batch completed.');
            })
            ->dispatch();
    }

    private function sendOrderStatusChange($userId, $order)
    {
        $orderAddress = OrderAddress::where('user_id', $userId)
            ->where('order_id', $order->id)
            ->with('address')
            ->first();

        if (!$orderAddress) {
            throw new Exception('Address not found for related order.', ErrorCode::NOT_FOUND);
        }

        $template = EmailTemplate::where('name', 'order_status_processing')->first();

        if (!$template) {
            Log::error('Email template not found.', ['templateName' => 'customer_note_added']);
            throw new Exception('Email template not found.', ErrorCode::NOT_FOUND);
        }

        $message = strtr($template->message, [
            '{FULLNAME}' => $order->user->full_name,
            '{ORDERID}' => $order->id,
            '{STATUS}' => Order::$orderStatusMapping[Order::READY_TO_SHIP]
        ]);

        $description = strtr($template->description, [
            '{FULLNAME}' => $order->user->full_name,
            '{ORDERID}' => $order->id,
            '{STATUS}' => Order::$orderStatusMapping[Order::READY_TO_SHIP]
        ]);

        $sendOrderNoteDTO = SendOrderNoteDTO::from([
            'title' => strtr($template->title, [
                '{ORDERID}' => $order->id,
            ]),
            'subject' => strtr($template->subject, [
                '{ORDERID}' => $order->id,
            ]),
            'message' => $message,
            'description' => $description,
        ]);

        return $sendOrderNoteDTO;
    }
}
