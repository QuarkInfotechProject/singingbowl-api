<?php

namespace Modules\User\App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\User\App\Emails\UserActivateMail;
use Modules\User\App\Events\SendActivateMail;

class SendActivateMailFired implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SendActivateMail $event): void
    {
        $sendRegisterEmailDTO = $event->sendRegisterEmailDTO;

        Mail::to($sendRegisterEmailDTO->email)->send(new UserActivateMail($sendRegisterEmailDTO));
    }
}
