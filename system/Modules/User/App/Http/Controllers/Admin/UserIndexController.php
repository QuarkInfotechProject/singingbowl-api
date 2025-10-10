<?php

namespace Modules\User\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\User\Service\Admin\UserIndexService;

class UserIndexController extends AdminBaseController
{
    function __construct(private UserIndexService $userIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $users = $this->userIndexService->index($request->all());

        return $this->successResponse('User has been fetched successfully.', $users);
    }
}
