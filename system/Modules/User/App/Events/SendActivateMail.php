<?php

namespace Modules\User\App\Events;

use Illuminate\Queue\SerializesModels;
use Modules\User\DTO\SendRegisterEmailDTO;

class SendActivateMail
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public SendRegisterEmailDTO $sendRegisterEmailDTO)
    {
        $this->sendRegisterEmailDTO = $sendRegisterEmailDTO;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
