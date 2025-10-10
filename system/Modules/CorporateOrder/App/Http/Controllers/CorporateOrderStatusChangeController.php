<?php

namespace Modules\CorporateOrder\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\CorporateOrder\Service\CorporateOrderChangeStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CorporateOrderStatusChangeController extends AdminBaseController
{
    function __construct(private CorporateOrderChangeStatusService $corporateOrderChangeStatusService)
    {
    }

    function __invoke(Request $request)
    {
        $this->corporateOrderChangeStatusService->changeStatus($request->all());

        return $this->successResponse('Status has been changed successfully.');
    }
}
