<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\User\App\Http\Requests\EmailVerificationRequest;
use Modules\User\Service\UserSendRegisterMailService;

class UserSendRegisterMailController extends UserBaseController
{
    function __construct(private UserSendRegisterMailService $userSendRegisterMailService)
    {
    }

    function __invoke(EmailVerificationRequest $request)
    {
        $this->userSendRegisterMailService->sendRegisterMail($request->input('email'));

        return $this->successResponse('Registration email has been sent successfully.');
    }
}
