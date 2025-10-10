<?php

namespace Modules\Order\App\Listeners;

use Modules\Order\App\Events\SendOrderInvoiceMail;
use Modules\Shared\Service\SmsService;

class SendOrderConfirmationSms
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function handle(SendOrderInvoiceMail $event): void
    {
        // $orderData = $event->orderData;

        // $name = $orderData['addressInformation']['name'];
        // $phoneNumber = $orderData['addressInformation']['mobile'];
        // $orderId = $orderData['id'];

        // $message = "Dear {$name}, Your order #{$orderId} has been placed. Thank you for shopping with ZOLPA STORE.";
        // $this->smsService->sendSms($phoneNumber, $message);
    }
}
