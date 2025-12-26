<?php

namespace Modules\User\Service;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Cart\Service\GuestCartService;
use Modules\Shared\Constant\SocialMediaConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\SystemConfiguration\App\Models\EmailTemplate;
use Modules\User\App\Events\SendRegistrationSuccessMail;
use Modules\User\App\Models\User;
use Modules\User\DTO\SendRegisterEmailDTO;

class UserGoogleLoginService
{
    private GuestCartService $guestCartService;
    private Request $request;

    public function __construct(GuestCartService $guestCartService, Request $request)
    {
        $this->guestCartService = $guestCartService;
        $this->request = $request;
    }

    /**
     * Handle Google login with user data from frontend.
     */
    public function login(array $data): array
    {
        try {
            $user = $this->findOrCreateUser($data);

            Auth::login($user);

            // Update last login
            $user->update(['last_login' => now()]);

            // Merge guest cart if token is present
            $guestToken = $this->request->header('X-Guest-Token');
            if ($guestToken) {
                try {
                    $this->guestCartService->mergeWithUserCart($guestToken, $user);
                    Log::info('Guest cart merged successfully for user during Google login.', [
                        'user_id' => $user->id,
                        'guest_token' => $guestToken
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to merge guest cart during Google login.', [
                        'user_id' => $user->id,
                        'guest_token' => $guestToken,
                        'error' => $e->getMessage()
                    ]);
                    // Don't throw - login should still proceed
                }
            }

            $tokenResult = $user->createToken($user->email);
            $token = property_exists($tokenResult, 'plainTextToken') 
                ? $tokenResult->plainTextToken 
                : $tokenResult->accessToken;

            $expiresAt = null;
            if (isset($tokenResult->accessToken) && 
                property_exists($tokenResult->accessToken, 'expires_at') && 
                $tokenResult->accessToken->expires_at) {
                $expiresAt = Carbon::parse($tokenResult->accessToken->expires_at)->toIso8601String();
            }

            return [
                'token' => $token,
                'expiresAt' => $expiresAt,
                'user' => [
                    'id' => $user->id,
                    'userId' => $user->uuid,
                    'email' => $user->email,
                    'fullName' => $user->full_name,
                    'profilePicture' => $user->profile_picture,
                ],
            ];
        } catch (\Exception $exception) {
            Log::error('Error handling Google login: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data
            ]);
            throw $exception;
        }
    }

    /**
     * Find existing user or create new one.
     */
    private function findOrCreateUser(array $data): User
    {
        // First try to find by google_id (oauth_id)
        $user = User::where('oauth_id', $data['google_id'])
            ->where('oauth_type', SocialMediaConstant::GOOGLE)
            ->first();

        if ($user) {
            if ($user->status !== User::STATUS_ACTIVE) {
                throw new Exception('User account is not active.', ErrorCode::FORBIDDEN);
            }
            return $user;
        }

        // Then try to find by email
        $user = User::where('email', $data['email'])->first();

        if ($user) {
            // User exists with this email
            if ($user->oauth_id && $user->oauth_type !== SocialMediaConstant::GOOGLE) {
                // User has a different OAuth provider
                throw new Exception(
                    'An account with this email already exists with a different login method.',
                    ErrorCode::CONFLICT
                );
            }

            if ($user->oauth_id === null) {
                // User exists with password login - link Google account
                DB::beginTransaction();
                try {
                    $user->update([
                        'oauth_id' => $data['google_id'],
                        'oauth_type' => SocialMediaConstant::GOOGLE,
                        'profile_picture' => $user->profile_picture ?? ($data['avatar'] ?? null),
                    ]);
                    DB::commit();
                    
                    Log::info('Linked Google account to existing user', [
                        'user_id' => $user->id,
                        'email' => $user->email
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

            if ($user->status !== User::STATUS_ACTIVE) {
                throw new Exception('User account is not active.', ErrorCode::FORBIDDEN);
            }

            return $user;
        }

        // Create new user
        DB::beginTransaction();
        try {
            $user = User::create([
                'uuid' => Str::uuid(),
                'email' => $data['email'],
                'full_name' => $data['name'],
                'profile_picture' => $data['avatar'] ?? null,
                'password' => null,
                'oauth_type' => SocialMediaConstant::GOOGLE,
                'oauth_id' => $data['google_id'],
                'status' => User::STATUS_ACTIVE,
            ]);

            DB::commit();

            // Send welcome email
            $this->sendOAuthWelcomeEmail($user);

            Log::info('Created new user via Google login', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return $user;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Error creating user during Google login: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data
            ]);
            throw $exception;
        }
    }

    /**
     * Send welcome email to newly created OAuth user.
     */
    private function sendOAuthWelcomeEmail(User $user): void
    {
        try {
            $template = EmailTemplate::where('name', 'oauth_welcome')->first()
                ?? EmailTemplate::where('name', 'user_created')->first();

            if (!$template) {
                Log::warning('No email template found for OAuth welcome email.', [
                    'user_id' => $user->id
                ]);
                return;
            }

            $providerName = 'Google';

            if ($template->name === 'oauth_welcome') {
                $message = strtr($template->message, [
                    '{FULLNAME}' => $user->full_name,
                    '{EMAIL}' => $user->email,
                    '{PROVIDER}' => $providerName
                ]);
                $subject = $template->subject;
            } else {
                $baseMessage = strtr($template->message, [
                    '{FULLNAME}' => $user->full_name,
                    '{EMAIL}' => $user->email
                ]);
                $message = "Welcome to Singing Bowl Village! Your account has been successfully created using your {$providerName} account. " . $baseMessage;
                $subject = "Welcome to Singing Bowl Village - Account Created";
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
                'template_used' => $template->name
            ]);
        } catch (\Exception $exception) {
            Log::error('Error sending OAuth welcome email: ' . $exception->getMessage(), [
                'exception' => $exception,
                'user_id' => $user->id
            ]);
            // Don't throw - login should still proceed
        }
    }
}
