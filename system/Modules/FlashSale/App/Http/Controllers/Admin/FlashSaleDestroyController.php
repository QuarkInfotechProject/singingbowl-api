<?php

namespace Modules\FlashSale\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\FlashSale\Service\Admin\FlashSaleDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FlashSaleDestroyController extends AdminBaseController
{
    function __construct(private flashSaleDestroyService $SaleDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->SaleDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('Flash Sale campaign has been deleted successfully.');
    }
}
