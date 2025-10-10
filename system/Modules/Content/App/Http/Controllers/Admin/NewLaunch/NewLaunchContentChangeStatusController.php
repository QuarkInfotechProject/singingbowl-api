<?php

namespace Modules\Content\App\Http\Controllers\Admin\NewLaunch;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\NewLaunch\NewLaunchContentChangeStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class NewLaunchContentChangeStatusController extends AdminBaseController
{
    function __construct(private NewLaunchContentChangeStatusService $newLaunchContentChangeStatusService)
    {
    }

    function __invoke(Request $request)
    {
        $this->newLaunchContentChangeStatusService->changeStatus($request->get('id'));

        return $this->successResponse('New launch content status has been changed successfully.');
    }
}
