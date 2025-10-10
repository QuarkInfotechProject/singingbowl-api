<?php

namespace Modules\Order\App\Http\Controllers\Admin;

use Modules\Order\Service\Admin\OrderShowLogService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class OrderShowLogController extends AdminBaseController
{
    function __construct(private OrderShowLogService $orderShowLogService)
    {
    }

    function __invoke(int $orderId)
    {
        $orderLog = $this->orderShowLogService->show($orderId);

        return $this->successResponse('Order log has been fetched successfully.', $orderLog);
    }
}
