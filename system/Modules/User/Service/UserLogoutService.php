<?php

namespace Modules\User\Service;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class UserLogoutService
{
    function logout()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                throw new Exception('No authenticated user found.', ErrorCode::UNAUTHORIZED);
            }

            $user->tokens->each(function ($token) {
                $token->delete();
            });
        } catch (\Exception $exception) {
            Log::error('Error during logout: ' . $exception->getMessage(), [
                'exception' => $exception,
                'user_id' => $user->id ?? null
            ]);
            throw $exception;
        }
    }
}
