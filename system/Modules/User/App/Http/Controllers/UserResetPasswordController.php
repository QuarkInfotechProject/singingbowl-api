<?php

namespace Modules\User\App\Http\Controllers;

use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\User\App\Http\Requests\UserResetPasswordRequest;
use Modules\User\Service\UserResetPasswordService;

class UserResetPasswordController extends UserBaseController
{
    function __construct(private UserResetPasswordService $userResetPasswordService)
    {
    }

    function __invoke(UserResetPasswordRequest $request)
    {
        $this->userResetPasswordService->resetPassword($request->all(),);

        return $this->successResponse('Password has been reset successfully.');
    }
}
