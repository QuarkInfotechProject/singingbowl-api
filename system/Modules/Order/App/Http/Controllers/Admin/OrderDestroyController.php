<?php

namespace Modules\Order\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Order\Service\Admin\OrderDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class OrderDestroyController extends AdminBaseController
{
    function __construct(private OrderDestroyService $orderDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->orderDestroyService->destroy($request->get('orderId'));

        return $this->successResponse('Order has been deleted successfully.');
    }
}
