<?php

namespace Modules\Content\App\Http\Controllers\Admin\NewLaunch;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\NewLaunch\NewLaunchContentDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class NewLaunchContentDestroyController extends AdminBaseController
{
    function __construct(private NewLaunchContentDestroyService $newLaunchContentDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->newLaunchContentDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('New launch content has been deleted successfully.');
    }
}
