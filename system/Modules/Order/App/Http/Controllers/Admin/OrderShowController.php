<?php

namespace Modules\Order\App\Http\Controllers\Admin;

use Modules\Order\Service\Admin\OrderShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class OrderShowController extends AdminBaseController
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
