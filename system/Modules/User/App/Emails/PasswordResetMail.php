<?php

namespace Modules\User\App\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\User\DTO\UserForgotPasswordDTO;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;
    public $otp;

    /**
     * Create a new message instance.
     */
    public function __construct(UserForgotPasswordDTO $data, $otp)
    {
        $this->data = $data;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject($this->data->subject)
            ->view('user::reset_password', ['data' => $this->data, 'otp' => $this->otp]);
    }
}
