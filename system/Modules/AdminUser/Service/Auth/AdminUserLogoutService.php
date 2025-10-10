<?php

namespace Modules\AdminUser\Service\Auth;

use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AdminUserLogoutService
{
    function logout($request)
    {
        $user = auth()->user();

        if (!$user) {
            throw new Exception('Admin user not authenticated.', ErrorCode::UNAUTHORIZED);
        }

        $request->user()->currentAccessToken()->delete();
    }
}
