<?php

namespace Modules\Order\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Order\App\Http\Requests\OrderChangeRequest;
use Modules\Order\Service\Admin\OrderChangeStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class OrderChangeStatusController extends AdminBaseController
{
    function __construct(private OrderChangeStatusService $orderChangeStatusService)
    {
    }

    function __invoke(OrderChangeRequest $request)
    {
        $this->orderChangeStatusService->changeOrderStatus($request->all());

        return $this->successResponse('Order status has been changed successfully.');
    }
}
