<?php

namespace Modules\User\App\Events;

use Illuminate\Queue\SerializesModels;
use Modules\User\DTO\UserForgotPasswordDTO;

class SendPasswordResetLinkMail
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public UserForgotPasswordDTO $userForgotPasswordDTO)
    {
        $this->userForgotPasswordDTO = $userForgotPasswordDTO;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
