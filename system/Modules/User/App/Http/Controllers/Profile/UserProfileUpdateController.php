<?php

namespace Modules\User\App\Http\Controllers\Profile;

use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\User\App\Http\Requests\UserProfileUpdateRequest;
use Modules\User\Service\Profile\UserProfileUpdateService;

class UserProfileUpdateController extends UserBaseController
{
    function __construct(private UserProfileUpdateService $userProfileUpdateService)
    {
    }

    function __invoke(UserProfileUpdateRequest $request)
    {
        $this->userProfileUpdateService->update($request->all());

        return $this->successResponse('User profile has been updated successfully.');
    }
}
