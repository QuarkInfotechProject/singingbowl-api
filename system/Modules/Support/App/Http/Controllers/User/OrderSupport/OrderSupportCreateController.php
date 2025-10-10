<?php

namespace Modules\Support\App\Http\Controllers\User\OrderSupport;

use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\Support\App\Http\Requests\OrderSupportCreateRequest;
use Modules\Support\Service\User\OrderSupport\OrderSupportCreateService;

class OrderSupportCreateController extends UserBaseController
{
    function __construct(private OrderSupportCreateService $orderSupportCreateService)
    {
    }

    function __invoke(OrderSupportCreateRequest $request)
    {
        $this->orderSupportCreateService->create($request->all());

        return $this->successResponse('Order support submitted successfully.');
    }
}
