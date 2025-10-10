<?php

namespace Modules\Order\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Order\App\Http\Requests\OrderCompleteRequest;
use Modules\Order\Service\User\OrderCompleteService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class OrderCompleteController extends UserBaseController
{
    function __construct(private OrderCompleteService $orderCompleteService)
    {
    }

    function __invoke(OrderCompleteRequest $request)
    {
        $this->orderCompleteService->completeOrder($request->all());

        return $this->successResponse('Order has been placed successfully.');
    }
}
