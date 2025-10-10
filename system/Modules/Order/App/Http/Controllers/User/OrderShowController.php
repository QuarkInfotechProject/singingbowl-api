<?php

namespace Modules\Order\App\Http\Controllers\User;

use Modules\Order\Service\User\OrderShowService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class OrderShowController extends UserBaseController
{
    function __construct(private OrderShowService $orderShowService)
    {
    }

    function __invoke(int $id)
    {
        $order = $this->orderShowService->show($id);

        return $this->successResponse('Order has been fetched successfully.', $order);
    }
}
