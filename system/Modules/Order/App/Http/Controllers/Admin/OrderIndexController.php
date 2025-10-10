<?php

namespace Modules\Order\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Order\Service\Admin\OrderIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class OrderIndexController extends AdminBaseController
{
    function __construct(private OrderIndexService $orderIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $orders = $this->orderIndexService->index($request->all());

        return $this->successResponse('Order has been fetched successfully.', $orders);
    }
}
