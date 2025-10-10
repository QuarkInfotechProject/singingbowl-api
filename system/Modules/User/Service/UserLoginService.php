<?php
namespace Modules\User\Service;
use Carbon\Carbon;
use Illuminate\Http\Request; // Added for request() access
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use MikeMcLin\WpPassword\Facades\WpPassword;
// use Modules\Cart\App\Models\Cart; // No longer directly needed here for session transfer
// use Modules\Cart\App\Models\CartItem; // No longer directly needed here for session transfer
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\User\App\Models\User;

class UserLoginService
{
    private const MAX_ATTEMPTS = 5;
    private const DECAY_MINUTES = 30;

    // Inject MergeCartsService
    public function __construct(private MergeCartsService $mergeCartsService)
    {
    }

    public function login(Request $request) // Type hint Request for direct access
    {
        try {
            $this->validateRequest($request);
            $this->checkTooManyFailedAttempts($request);
            $user = $this->findActiveUser($request->email);
            if (!$user) {
                $this->handleFailedAttempt($request);
                throw new Exception('Invalid login credentials', ErrorCode::UNAUTHORIZED);
            }
            $user->update(['last_login' => now()]);
            if ($user->status === User::STATUS_BLOCKED) {
                throw new Exception(
                    'Your account is currently blocked. Please contact support for assistance.',
                    ErrorCode::FORBIDDEN
                );
            }
            if (!$this->verifyPassword($user, $request->password)) {
                $this->handleFailedAttempt($request);
                throw new Exception('Wrong password, please try again.', ErrorCode::UNAUTHORIZED);
            }
            return $this->handleSuccessfulLogin($user, $request); // Pass the full request
        } catch (\Exception $exception) {
            Log::error('Login error', [
                'message' => $exception->getMessage(),
                'email' => $request->email,
                'trace' => $exception->getTraceAsString()
            ]);
            throw $exception;
        }
    }

    private function validateRequest(Request $request): void
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    }

    private function findActiveUser(string $email): ?User
    {
        $user = User::where('email', $email)
            ->where('status', User::STATUS_ACTIVE)
            ->first();
        if ($user && $user->oauth_id) {
            throw new Exception(
                'This account is linked with Google. Please sign in using Google.',
                ErrorCode::UNAUTHORIZED
            );
        }

        return $user;
    }

    private function verifyPassword(User $user, string $password): bool
    {
        // Check WordPress password
        if ($this->isWordPressHash($user->password) && WpPassword::check($password, $user->password)) {
            $this->upgradeToLaravelHash($user, $password);
            return true;
        }
        // Check Laravel password
        return Hash::check($password, $user->password);
    }

    private function isWordPressHash(string $hash): bool
    {
        return strlen($hash) === 34 && str_starts_with($hash, '$P$');
    }

    private function upgradeToLaravelHash(User $user, string $password): void
    {
        $user->password = Hash::make($password);
        $user->save();
    }

    private function handleSuccessfulLogin(User $user, Request $request): array // Changed to accept Request
    {
        RateLimiter::clear($this->throttleKey($request));
        Auth::login($user);

        // Merge guest cart if token is present
        $guestToken = $request->header('X-Guest-Token');
        if ($guestToken) {
            try {
                $this->mergeCartsService->execute($guestToken, $user);
                Log::info('Guest cart merged successfully for user.', ['user_id' => $user->id, 'guest_token' => $guestToken]);
            } catch (\Exception $e) {
                Log::error('Failed to merge guest cart during login.', [
                    'user_id' => $user->id,
                    'guest_token' => $guestToken,
                    'error' => $e->getMessage()
                ]);
                // Decide if this error should prevent login or just be logged.
                // For now, it's just logged, login proceeds.
            }
        }

        $tokenResult = $user->createToken($request->email);
        $token = $tokenResult->accessToken ?? $tokenResult->plainTextToken ?? null;

        $expiresAt = null;
        if (isset($tokenResult->accessToken) && is_object($tokenResult->accessToken) &&
            property_exists($tokenResult->accessToken, 'expires_at') &&
            $tokenResult->accessToken->expires_at) {

            $expiresAt = Carbon::parse($tokenResult->accessToken->expires_at)->toIso8601String();
        }

        return [
            'token' => $token,
            'expiresAt' => $expiresAt,
            'user' => [
                'name' => $user->full_name,
                'userId' => $user->uuid,
                'isUserLoggedIn' => $user->status === User::STATUS_ACTIVE,
            ],
        ];
    }

    private function handleFailedAttempt(Request $request): void
    {
        RateLimiter::hit($this->throttleKey($request));
    }

    private function checkTooManyFailedAttempts(Request $request): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($request), self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($this->throttleKey($request));
            throw ValidationException::withMessages([
                'email' => [trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ])],
            ])->status(ErrorCode::TOO_MANY_REQUESTS);
        }
    }

    private function throttleKey(Request $request): string
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }
}