<?php

namespace Modules\Order\App\Http\Controllers\User;

use Modules\Order\App\Http\Requests\OrderFailureRequest;
use Modules\Order\Service\User\OrderPaymentFailService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class OrderPaymentFailController extends UserBaseController
{
    public function __construct(private OrderPaymentFailService $orderPaymentFailService)
    {
    }

    public function __invoke(OrderFailureRequest $request)
    {
        $this->orderPaymentFailService->handleFailure($request->all());

        return $this->successResponse('Payment cancelled.');
    }
}
