<?php

namespace Modules\User\App\Events;

use Illuminate\Queue\SerializesModels;
use Modules\User\App\Models\User;

class UserRegistered
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public User $user)
    {
        $this->user = $user;
    }
}
