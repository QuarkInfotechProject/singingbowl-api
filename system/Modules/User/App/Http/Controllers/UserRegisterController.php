<?php

namespace Modules\User\App\Http\Controllers;

use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\User\App\Http\Requests\UserRegisterRequest;
use Modules\User\DTO\UserRegisterDTO;
use Modules\User\Service\UserRegisterService;

class UserRegisterController extends UserBaseController
{
    function __construct(private UserRegisterService $userRegisterService)
    {
    }

    function __invoke(UserRegisterRequest $request)
    {
        $userRegisterDTO = UserRegisterDTO::from($request->request->all());

        $token = $this->userRegisterService->register($userRegisterDTO);

        return $this->successResponse('User has been registered successfully.', $token);
    }
}
