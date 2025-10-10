<?php

namespace Modules\User\App\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\User\DTO\SendRegisterEmailDTO;

class UserRegistrationSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $sendRegisterEmailDTO;

    /**
     * Create a new message instance.
     */
    public function __construct(SendRegisterEmailDTO $sendRegisterEmailDTO)
    {
        $this->sendRegisterEmailDTO = $sendRegisterEmailDTO;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject($this->sendRegisterEmailDTO->subject)
            ->view('user::user_registration_success', ['sendRegisterEmailDTO' => $this->sendRegisterEmailDTO]);
    }
}
