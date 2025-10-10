<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\User\Service\UserLoginService;

class UserLoginController extends UserBaseController
{
    function __construct(private UserLoginService $userLoginService)
    {
    }

    function __invoke(Request $request)
    {
        $token = $this->userLoginService->login($request);

        return $this->successResponse('User has been logged in successfully.', $token);
    }
}
