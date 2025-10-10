<?php

namespace Modules\User\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\User\Service\Admin\UserBlockService;

class UserBlockController extends AdminBaseController
{
    function __construct(private UserBlockService $userBlockService)
    {
    }

    function __invoke(Request $request)
    {
        $this->userBlockService->block($request->all(), $request->getClientIp(), $request->userAgent());

        return $this->response('User has been blocked successfully.');
    }
}
