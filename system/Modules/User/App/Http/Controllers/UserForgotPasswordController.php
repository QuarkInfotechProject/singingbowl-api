<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\User\Service\UserForgotPasswordService;

class UserForgotPasswordController extends UserBaseController
{
    function __construct(private UserForgotPasswordService $userForgotPasswordService)
    {
    }

    function __invoke(Request $request)
    {
        $this->userForgotPasswordService->resetPassword($request);

        return $this->response('Email to reset password has been sent successfully.');
    }
}
