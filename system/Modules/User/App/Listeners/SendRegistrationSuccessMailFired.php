<?php

namespace Modules\User\App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\User\App\Emails\UserRegistrationSuccessMail;
use Modules\User\App\Events\SendRegistrationSuccessMail;

class SendRegistrationSuccessMailFired implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SendRegistrationSuccessMail $event): void
    {
        $sendRegisterEmailDTO = $event->sendRegisterEmailDTO;

        Mail::to($sendRegisterEmailDTO->email)->send(new UserRegistrationSuccessMail($sendRegisterEmailDTO));
    }
}
