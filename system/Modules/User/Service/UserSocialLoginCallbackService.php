<?php

namespace Modules\User\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Modules\Cart\Service\GuestCartService;
use Modules\Shared\Constant\SocialMediaConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\SystemConfiguration\App\Models\EmailTemplate;
use Modules\User\App\Events\SendRegistrationSuccessMail;
use Modules\User\App\Models\User;
use Modules\User\DTO\SendRegisterEmailDTO;
use Carbon\Carbon;

class UserSocialLoginCallbackService
{
    private GuestCartService $guestCartService;
    private Request $request;

    public function __construct(GuestCartService $guestCartService, Request $request)
    {
        $this->guestCartService = $guestCartService;
        $this->request = $request;
    }

    public function handleSocialLoginCallback(string $provider)
    {
        try {
            $this->validateProvider($provider);

            $socialUser = Socialite::driver($provider)->stateless()->user();

            $user = $this->findOrCreateUser($socialUser, $provider);

            Auth::login($user);

            // Merge guest cart if token is present
            $guestToken = $this->request->header('X-Guest-Token');
            if ($guestToken) {
                try {
                    $this->guestCartService->mergeWithUserCart($guestToken, $user);
                    Log::info('Guest cart merged successfully for user during social login.', ['user_id' => $user->id, 'guest_token' => $guestToken]);
                } catch (\Exception $e) {
                    Log::error('Failed to merge guest cart during social login.', [
                        'user_id' => $user->id,
                        'guest_token' => $guestToken,
                        'error' => $e->getMessage()
                    ]);
                    // Log error, but social login proceeds
                }
            }

            $tokenResult = $user->createToken($user->email);
            $token = property_exists($tokenResult, 'plainTextToken') ? $tokenResult->plainTextToken : $tokenResult->accessToken;

            $expiresAt = null;
            if (isset($tokenResult->accessToken) && property_exists($tokenResult->accessToken, 'expires_at') && $tokenResult->accessToken->expires_at) {
                 $expiresAt = Carbon::parse($tokenResult->accessToken->expires_at)->toIso8601String();
            }

            return [
                'token' => $token,
                'expiresAt' => $expiresAt,
                'user' => [
                    'name' => $user->full_name,
                    'userId' => $user->uuid,
                    'email' => $user->email,
                ],
            ];
        } catch (\Exception $exception) {
            Log::error('Error handling social login callback: ' . $exception->getMessage(), [
                'exception' => $exception,
                'provider' => $provider,
                'request_data' => $this->request->all() // Added for more context
            ]);
            throw $exception;
        }
    }

    private function validateProvider(string $provider)
    {
        if ($provider !== SocialMediaConstant::GOOGLE) {
            throw new Exception('Social Provider not found.', ErrorCode::BAD_REQUEST);
        }
    }

    private function findOrCreateUser($socialUser, string $provider): User
    {
        try {
            $user = User::where('email', $socialUser->email)->first();

            if ($user) {
                if ($user->oauth_id && $user->oauth_type === $provider) {
                    if ($user->status !== User::STATUS_ACTIVE) {
                        throw new Exception('User account is not active.', ErrorCode::FORBIDDEN);
                    }
                    return $user;
                } else {
                    throw new Exception('An account with this email already exists. Please log in using your password or link your social account if supported.', ErrorCode::CONFLICT);
                }
            }
            DB::beginTransaction();
            try {
                $user = $this->createUser($socialUser, $provider);
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                Log::error('Error creating user during social login: ' . $exception->getMessage(), [
                    'exception' => $exception,
                    'socialUser' => (array) $socialUser,
                    'provider' => $provider
                ]);
                throw $exception;
            }

            return $user;
        } catch (\Exception $exception) {
            Log::error('Error finding or creating user during social login: ' . $exception->getMessage(), [
                'exception' => $exception,
                'socialUser' => (array) $socialUser,
                'provider' => $provider
            ]);
            throw $exception;
        }
    }

    private function createUser($socialUser, string $provider): User
    {
        try {
            $user = User::create([
                'uuid' => Str::uuid(),
                'email' => $socialUser->email,
                'full_name' => $socialUser->name,
                'profile_picture' => $socialUser->avatar,
                'password' => null,
                'oauth_type' => $provider,
                'oauth_id' => $socialUser->id,
                'status' => User::STATUS_ACTIVE,
            ]);

            // Send welcome email to OAuth user
            $this->sendOAuthWelcomeEmail($user, $provider);

            return $user;
        } catch (\Exception $exception) {
            Log::error('Error creating user record for social login: ' . $exception->getMessage(), [
                'exception' => $exception,
                'socialUser' => (array) $socialUser,
                'provider' => $provider
            ]);
            throw $exception;
        }
    }

    /**
     * Send welcome email to newly created OAuth user
     */
    private function sendOAuthWelcomeEmail(User $user, string $provider): void
    {
        try {
            // Try to get OAuth-specific template first, fallback to general user_created
            $template = EmailTemplate::where('name', 'oauth_welcome')->first()
                       ?? EmailTemplate::where('name', 'user_created')->first();

            if (!$template) {
                Log::warning('No email template found for OAuth welcome email.', [
                    'user_id' => $user->id,
                    'provider' => $provider
                ]);
                return;
            }

            $providerName = ucfirst(strtolower($provider));

            // Use OAuth template if available, otherwise customize the general template
            if ($template->name === 'oauth_welcome') {
                $message = strtr($template->message, [
                    '{FULLNAME}' => $user->full_name,
                    '{EMAIL}' => $user->email,
                    '{PROVIDER}' => $providerName
                ]);
                $subject = $template->subject;
            } else {
                // Fallback to customized general template
                $baseMessage = strtr($template->message, [
                    '{FULLNAME}' => $user->full_name,
                    '{EMAIL}' => $user->email
                ]);
                $message = "Welcome to ZOLPA STORE! Your account has been successfully created using your {$providerName} account. " . $baseMessage;
                $subject = "Welcome to ZOLPA STORE - Account Created";
            }

            $sendEmailDTO = SendRegisterEmailDTO::from([
                'title' => $template->title,
                'subject' => $subject,
                'description' => $message,
                'email' => $user->email,
            ]);

            Event::dispatch(new SendRegistrationSuccessMail($sendEmailDTO));

            Log::info('OAuth welcome email sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'provider' => $provider,
                'template_used' => $template->name
            ]);

        } catch (\Exception $exception) {
            Log::error('Error sending OAuth welcome email: ' . $exception->getMessage(), [
                'exception' => $exception,
                'user_id' => $user->id,
                'provider' => $provider
            ]);
            // Don't throw exception to avoid breaking OAuth flow
        }
    }
}
