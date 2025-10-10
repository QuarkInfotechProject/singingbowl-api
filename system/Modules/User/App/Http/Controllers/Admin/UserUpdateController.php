<?php

namespace Modules\User\App\Http\Controllers\Admin;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\User\App\Http\Requests\UserUpdateRequest;
use Modules\User\Service\Admin\UserUpdateService;

class UserUpdateController extends AdminBaseController
{
    function __construct(private UserUpdateService $userUpdateService)
    {
    }

    function __invoke(UserUpdateRequest $request)
    {
        $this->userUpdateService->update($request->all());

        return $this->successResponse('User has been updated successfully.');
    }
}
