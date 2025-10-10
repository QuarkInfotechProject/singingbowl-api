<?php

namespace Modules\User\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\User\Service\Admin\UserActivateService;

class UserActivateController extends AdminBaseController
{
    public function __construct(private UserActivateService $userActivateService)
    {
    }

    public function __invoke(Request $request)
    {
        $this->userActivateService->activate($request->all(), $request->getClientIp());

        return $this->successResponse('User has been activated successfully.');
    }
}
