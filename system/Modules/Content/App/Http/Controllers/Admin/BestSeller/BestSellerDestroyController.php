<?php

namespace Modules\Content\App\Http\Controllers\Admin\BestSeller;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\BestSeller\BestSellerDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class BestSellerDestroyController extends AdminBaseController
{
    function __construct(private BestSellerDestroyService $bestSellerDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->bestSellerDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('Content has been deleted successfully.');
    }
}
