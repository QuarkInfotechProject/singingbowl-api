<?php

namespace Modules\User\App\Listeners;

use Modules\Shared\Service\SmsService;
use Modules\User\App\Events\UserRegistered;

class SendWelcomeSms
{

    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        // $user = $event->user;
        // $message = "Hello {$user->full_name}, Thank you for joining ZOLPA STORE Community. We are happy to have you on board. Call us on 01-5313291 if you need any help.";
        // $this->smsService->sendSms($user->phone_no, $message);
    }
}
