<?php

namespace Modules\User\App\Http\Controllers;

use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\User\Service\UserLogoutService;

class UserLogoutController extends UserBaseController
{
    function __construct(private UserLogoutService $userLogoutService)
    {
    }

    function __invoke()
    {
        $this->userLogoutService->logout();

        return $this->successResponse('User has been logged out successfully.');
    }
}
