<?php

namespace Modules\User\App\Http\Controllers\Profile;

use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\User\Service\Profile\UserProfileShowService;

class UserProfileShowController extends UserBaseController
{
    function __construct(private UserProfileShowService $userProfileShowService)
    {
    }

    function __invoke()
    {
        $user = $this->userProfileShowService->show();

        return $this->successResponse('User profile has been fetched successfully.', $user);
    }
}
