<?php

namespace Modules\Content\App\Http\Controllers\Admin\BestSeller;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\BestSeller\BestSellerChangeStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class BestSellerChangeStatusController extends AdminBaseController
{
    function __construct(private BestSellerChangeStatusService $bestSellerChangeStatusService)
    {
    }

    function __invoke(Request $request)
    {
        $this->bestSellerChangeStatusService->changeStatus($request->get('id'));

        return $this->successResponse('Best seller content status has been changed successfully.');
    }
}
