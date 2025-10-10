<?php

namespace Modules\Order\App\Http\Controllers\Admin;

use Modules\Order\Service\Admin\OrderShowRefundDataService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class OrderShowRefundDataController extends AdminBaseController
{
    function __construct(private OrderShowRefundDataService $orderShowRefundDataService)
    {
    }

    function __invoke(int $orderId)
    {
        $orderRefundData = $this->orderShowRefundDataService->show($orderId);

        return $this->successResponse('Order refund data has been fetched successfully.', $orderRefundData);
    }
}
