<?php

namespace Modules\Order\App\Http\Controllers\User;

use Modules\Order\Service\User\OrderIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class OrderIndexController extends UserBaseController
{
    function __construct(private OrderIndexService $orderIndexService)
    {
    }

    function __invoke()
    {
        $orders = $this->orderIndexService->index();

        return $this->successResponse('Orders has been fetched successfully.', $orders);
    }
}
