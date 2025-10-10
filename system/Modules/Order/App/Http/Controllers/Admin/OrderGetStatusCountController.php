<?php

namespace Modules\Order\App\Http\Controllers\Admin;

use Modules\Order\Service\Admin\OrderGetStatusCountService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class OrderGetStatusCountController extends AdminBaseController
{
    function __construct(private OrderGetStatusCountService $orderGetStatusCountService)
    {
    }

    function __invoke()
    {
        $status = $this->orderGetStatusCountService->getStatusCount();

        return $this->successResponse('Order status count has been fetched successfully.', $status);
    }
}
