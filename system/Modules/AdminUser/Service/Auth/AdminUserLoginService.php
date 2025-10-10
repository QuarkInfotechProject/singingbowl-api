<?php

namespace Modules\AdminUser\Service\Auth;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Modules\AdminUser\App\Models\AdminUser;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AdminUserLoginService
{
    function login($request)
    {
        $this->checkTooManyFailedAttempts();

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = AdminUser::where('email', $request->input('email'))
            ->first();

        if (!$user) {
            throw new Exception('Admin user not found.', ErrorCode::NOT_FOUND);
        }

        if ($user->status == AdminUser::INACTIVE) {
            throw new Exception('Your account is currently blocked. Please contact support for assistance.', ErrorCode::FORBIDDEN);
        }

        if (!auth()->attempt($request->only(['email', 'password']))) {
            RateLimiter::hit($this->throttleKey(), $seconds = 1800);
            throw new Exception('Email & Password do not match our records.', ErrorCode::UNAUTHORIZED);
        }

        RateLimiter::clear($this->throttleKey());

        $authenticatedUser = Auth::user();

        $token = $request->user()->createToken(
            'personal-access-client',
            ['*'],
            now()->addMonth(1)
        );

        return [
            'token' => $token->plainTextToken,
            'ExpiresAt' => Carbon::parse($token->accessToken->expires_at)->format('Y-m-d H:i:s'),
            'user' => [
                'userId' => $authenticatedUser->id,
                'groupId' => $authenticatedUser->roles->first()->id ?? null,
                'name' => $authenticatedUser->name,
                'email' => $authenticatedUser->email,
                'isSuperAdmin' => $authenticatedUser->super_admin,
            ],
        ];
    }

    private function throttleKey()
    {
        return Str::lower(request('email'));
    }

    private function checkTooManyFailedAttempts()
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey(), 10)) {
            throw new Exception('Too many login attempts.', ErrorCode::TOO_MANY_ATTEMPTS);
        }
    }
}
