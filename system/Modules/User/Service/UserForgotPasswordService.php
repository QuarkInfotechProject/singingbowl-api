<?php

namespace Modules\User\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\SystemConfiguration\App\Models\EmailTemplate;
use Modules\SystemConfiguration\App\Models\SystemConfig;
use Modules\User\App\Events\SendPasswordResetLinkMail;
use Modules\User\App\Models\User;
use Modules\User\App\Models\VerificationCode;
use Modules\User\DTO\UserForgotPasswordDTO;

class UserForgotPasswordService
{
    function resetPassword($request)
    {
        $validateEmail = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validateEmail['email'])->first();

        if (!$user) {
            throw new Exception('Email not found.', ErrorCode::NOT_FOUND);
        }

        $otp = mt_rand(100000, 999999);
        $minutes = (int) (SystemConfig::where('name', 'code_expiration_time')->value('value') ?? 10);

        try {
            DB::beginTransaction();

            VerificationCode::updateOrCreate([
                'email' => $validateEmail['email']
            ], [
                'code' => $otp,
                'expires_at' => now()->addMinutes($minutes),
                'attempts' => 0
            ]);

            $this->sendPasswordResetEmail($user, $validateEmail['email'], $otp);
            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error resetting password: ' . $exception->getMessage(), [
                'exception' => $exception,
                'validateEmail' => $validateEmail,
                'user' => $user->id,
            ]);
            DB::rollBack();
            throw $exception;
        }
    }

    private function sendPasswordResetEmail(User $user, $email, $otp)
    {
        $template = EmailTemplate::where('name', 'end_user_forgot_password')->first();

        $message = strtr($template->message, [
            '{FULLNAME}' => $user['full_name'],
            '{OTP_CODE}' => $otp,
        ]);

        $userForgotPasswordDTO = UserForgotPasswordDTO::from([
            'title' => $template->title,
            'subject' => $template->subject,
            'description' => $message,
            'email' => $email,
            'token' => $otp
        ]);

        Event::dispatch(new SendPasswordResetLinkMail($userForgotPasswordDTO));
    }
}
