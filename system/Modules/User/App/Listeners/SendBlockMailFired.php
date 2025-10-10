<?php

namespace Modules\User\App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\User\App\Emails\UserBlockMail;
use Modules\User\App\Emails\UserRegistrationMail;
use Modules\User\App\Events\SendBlockMail;

class SendBlockMailFired implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SendBlockMail $event): void
    {
        $sendRegisterEmailDTO = $event->sendRegisterEmailDTO;

        Mail::to($sendRegisterEmailDTO->email)->send(new UserBlockMail($sendRegisterEmailDTO));
    }
}
