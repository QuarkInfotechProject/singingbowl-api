<?php

namespace Modules\User\App\Http\Controllers;

use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\User\Service\UserSocialLoginRedirectService;

class UserSocialLoginRedirectController extends UserBaseController
{
    public function __construct(private UserSocialLoginRedirectService $userSocialLoginRedirectService)
    {
    }

    public function __invoke(string $provider)
    {
        $url = $this->userSocialLoginRedirectService->handleSocialLoginRedirect($provider);

        return $this->successResponse('Redirect successful.', $url);
    }
}
