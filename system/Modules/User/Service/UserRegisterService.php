<?php

namespace Modules\User\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Cart\Service\GuestCartService;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\SystemConfiguration\App\Models\EmailTemplate;
use Modules\SystemConfiguration\App\Models\SystemConfig;
use Modules\User\App\Events\SendRegistrationSuccessMail;
use Modules\User\App\Events\UserRegistered;
use Modules\User\App\Models\User;
use Modules\User\App\Models\VerificationCode;
use Modules\User\DTO\SendRegisterEmailDTO;
use Modules\User\DTO\UserRegisterDTO;
use Carbon\Carbon;

class UserRegisterService
{
    public function __construct(private GuestCartService $guestCartService, private Request $request)
    {
    }

    function register(UserRegisterDTO $userRegisterDTO)
    {
        try {
            $verification = VerificationCode::where('email', $userRegisterDTO->email)
                ->latest()
                ->first();

            if (!$verification) {
                throw new Exception('Email not found.', ErrorCode::NOT_FOUND);
            }

            $codeAttempts = SystemConfig::firstWhere('name', 'code_attempts')->value('value');

            if ($verification->attempts >= $codeAttempts) {
                throw new Exception('Too many verification code attempts.', ErrorCode::TOO_MANY_REQUESTS);
            }

            if ($verification->expires_at < now()) {
                throw new Exception('Verification code has already expired!', ErrorCode::GONE);
            }

            if ($verification->code != $userRegisterDTO->verificationCode) {
                $verification->attempts++;
                $verification->save();
                throw new Exception('Verification code doesn\'t match.', ErrorCode::BAD_REQUEST);
            }

            return DB::transaction(function () use ($userRegisterDTO, $verification) {
                $user = User::create([
                    'uuid' => Str::uuid()->toString(),
                    'email' => $userRegisterDTO->email,
                    'full_name' => ucwords($userRegisterDTO->fullName),
                    'phone_no' => $userRegisterDTO->phoneNumber,
                    'password' => bcrypt($userRegisterDTO->password),
                    'status' => User::STATUS_ACTIVE,
                ]);

                $verification->delete();

                $guestToken = $this->request->header('X-Guest-Token');
                if ($guestToken) {
                    try {
                        $this->guestCartService->mergeWithUserCart($guestToken, $user);
                        Log::info('Guest cart merged successfully for user during registration.', ['user_id' => $user->id, 'guest_token' => $guestToken]);
                    } catch (\Exception $e) {
                        Log::error('Failed to merge guest cart during registration.', [
                            'user_id' => $user->id,
                            'guest_token' => $guestToken,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                $tokenResult = $user->createToken($userRegisterDTO->email);
                $token = $tokenResult->accessToken;
                if (property_exists($tokenResult, 'plainTextToken')) {
                    $token = $tokenResult->plainTextToken;
                }

                $expiresAt = null;
                if (isset($tokenResult->accessToken) && property_exists($tokenResult->accessToken, 'expires_at') && $tokenResult->accessToken->expires_at) {
                     $expiresAt = Carbon::parse($tokenResult->accessToken->expires_at)->toIso8601String();
                }

                $this->sendUserRegistrationSuccessMail($user);
                Event::dispatch(new UserRegistered($user));

                return [
                    'token' => $token,
                    'expiresAt' => $expiresAt,
                    'user' => [
                        'name' => $userRegisterDTO->fullName,
                        'userId' => $user->uuid,
                    ],
                ];
            });
        } catch (\Exception $exception) {
            Log::error('Error during user registration: ' . $exception->getMessage(), [
                'exception' => $exception,
                'userRegisterDTO' => $userRegisterDTO->toArray()
            ]);
            throw $exception;
        }
    }

    private function sendUserRegistrationSuccessMail($endUser) {
        $template = EmailTemplate::where('name', 'user_created')->first();

        if (!$template) {
            Log::warning('Email template \'user_created\' not found. Cannot send registration success email.', ['user_id' => $endUser->id]);
            return;
        }

        $message = strtr($template->message, [
            '{FULLNAME}' => $endUser->full_name,
            '{EMAIL}' => $endUser->email
        ]);

        $sendEmailDTO = SendRegisterEmailDTO::from([
            'title' => $template->title,
            'subject' => $template->subject,
            'description' => $message,
            'email' => $endUser->email,
        ]);

        Event::dispatch(new SendRegistrationSuccessMail($sendEmailDTO));
    }
}
