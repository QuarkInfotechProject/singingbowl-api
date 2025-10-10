<?php

namespace Modules\User\App\Http\Controllers;

use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\User\Service\UserSocialLoginCallbackService;

class UserSocialLoginCallbackController extends UserBaseController
{
    public function __construct(private UserSocialLoginCallbackService $userSocialLoginCallbackService)
    {
    }

    public function __invoke(string $provider)
    {
        $token = $this->userSocialLoginCallbackService->handleSocialLoginCallback($provider);

        return $this->successResponse('Successful.', $token);
    }
}
