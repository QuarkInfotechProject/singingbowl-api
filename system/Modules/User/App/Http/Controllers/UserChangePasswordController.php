<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\User\App\Http\Requests\UserChangePasswordRequest;
use Modules\User\DTO\UserChangePasswordDTO;
use Modules\User\Service\UserChangePasswordService;

class UserChangePasswordController extends UserBaseController
{
    function __construct(private UserChangePasswordService $userChangePasswordService)
    {
    }

    function __invoke(UserChangePasswordRequest $request)
    {
        $user = Auth::guard('user')->user();

        $userChangePasswordDTO = UserChangePasswordDTO::from($request->all());

        $this->userChangePasswordService->changePassword($userChangePasswordDTO, $user);

        return $this->successResponse('Password has been changed successfully.');
    }
}
