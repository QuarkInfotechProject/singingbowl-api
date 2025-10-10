<?php

namespace Modules\Content\App\Http\Controllers\Admin\InThePress;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\InThePress\InThePressChangeStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class InThePressChangeStatusController extends AdminBaseController
{
    function __construct(private InThePressChangeStatusService $inThePressChangeStatusService)
    {
    }

    function __invoke(Request $request)
    {
        $this->inThePressChangeStatusService->changeStatus($request->get('id'));

        return $this->successResponse('Content status has been changed successfully.');
    }
}
