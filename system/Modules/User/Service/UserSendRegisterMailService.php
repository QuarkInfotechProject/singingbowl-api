<?php
namespace Modules\User\Service;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\SystemConfiguration\App\Models\EmailTemplate;
use Modules\SystemConfiguration\App\Models\SystemConfig;
use Modules\User\App\Events\SendRegisterMail;
use Modules\User\App\Models\User;
use Modules\User\App\Models\VerificationCode;
use Modules\User\DTO\SendRegisterEmailDTO;
class UserSendRegisterMailService
{
    function sendRegisterMail(string $email)
    {
        try {
            $user = User::where('email', $email)->first();
            if ($user) {
                if ($user->oauth_id) {
                    throw new Exception(
                        'This account is already linked with Google. Please sign in using Google.',
                        ErrorCode::CONFLICT
                    );
                }

                throw new Exception('Email is already registered.', ErrorCode::UNPROCESSABLE_CONTENT);
            }

            $result = $this->saveVerificationCode($email);
            $template = EmailTemplate::where('name', 'user_registration')->first();
            if (!$template) {
                throw new Exception('Email template not found.', ErrorCode::NOT_FOUND);
            }

            $message = strtr($template->message, [
                '{CODE}' => $result->code
            ]);

            $sendRegisterEmailDTO = SendRegisterEmailDTO::from([
                'system' => $template->name,
                'title' => $template->title,
                'subject' => $template->subject,
                'image' => $template->image,
                'description' => $message,
                'email' => $email,
                'code' => $result->code
            ]);

            Event::dispatch(new SendRegisterMail($sendRegisterEmailDTO));
        } catch (\Exception $exception) {
            Log::error('Error sending registration mail: ' . $exception->getMessage(), [
                'exception' => $exception,
                'email' => $email
            ]);
            throw $exception;
        }
    }

    function saveVerificationCode($email)
    {
        try {
            //$minutes = SystemConfig::where('name', 'code_expiration_time')->pluck('value')->first();

            // $minutes = SystemConfig::where('name', 'code_expiration_time')->pluck('value')->first();
            return VerificationCode::updateOrCreate([
                'email' => $email
            ], [
                'code' => mt_rand(100000, 999999),
                'expires_at' => now()->addMinutes(15),
            ]);
        } catch (\Exception $exception) {
            Log::error('Error saving verification code: ' . $exception->getMessage(), [
                'exception' => $exception,
                'email' => $email
            ]);
            throw $exception;
        }
    }
}