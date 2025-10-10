<?php

namespace Modules\Order\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Order\Service\User\OrderCancelService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class OrderCancelController extends UserBaseController
{
    function __construct(private OrderCancelService $orderCancelService)
    {
    }

    function __invoke(Request $request)
    {
        $this->orderCancelService->cancelOrder($request->get('orderId'));

        return $this->successResponse('Order has been cancelled successfully.');
    }
}
