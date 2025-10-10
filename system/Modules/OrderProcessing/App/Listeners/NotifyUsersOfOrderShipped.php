<?php

namespace Modules\OrderProcessing\App\Listeners;

use Modules\OrderProcessing\App\Events\OrderShipped;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Shared\Service\SmsService;

class NotifyUsersOfOrderShipped
{
    protected $smsService;
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderShipped $event): void
    {
        $firstName = $event->firstName;
        $orderId = $event->orderId;
        $mobile = $event->mobile;


        $message = "Dear {$firstName}, Your order #{$orderId} from ZOLPA STORE has been Shipped. Track your order at: http://localhost:98/admin/track/$orderId/$mobile";
        $this->smsService->sendSms($mobile, $message);
    }
}
