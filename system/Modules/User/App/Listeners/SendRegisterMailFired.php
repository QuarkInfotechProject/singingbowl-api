<?php

namespace Modules\User\App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\User\App\Emails\UserRegistrationMail;
use Modules\User\App\Events\SendRegisterMail;

class SendRegisterMailFired implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SendRegisterMail $event): void
    {
        $sendRegisterEmailDTO = $event->sendRegisterEmailDTO;

        Mail::to($sendRegisterEmailDTO->email)->send(new UserRegistrationMail($sendRegisterEmailDTO));
    }
}
