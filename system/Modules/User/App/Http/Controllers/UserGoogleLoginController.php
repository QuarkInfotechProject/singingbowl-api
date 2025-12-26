<?php

namespace Modules\User\App\Http\Controllers;

use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\User\App\Http\Requests\GoogleLoginRequest;
use Modules\User\Service\UserGoogleLoginService;

class UserGoogleLoginController extends UserBaseController
{
    public function __construct(private UserGoogleLoginService $userGoogleLoginService)
    {
    }

    public function __invoke(GoogleLoginRequest $request)
    {
        $result = $this->userGoogleLoginService->login($request->validated());

        return $this->successResponse('Login successful', $result);
    }
}
