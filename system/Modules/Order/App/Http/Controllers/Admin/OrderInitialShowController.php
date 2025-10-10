<?php

namespace Modules\Order\App\Http\Controllers\Admin;

use Modules\Order\Service\Admin\OrderInitialShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class OrderInitialShowController extends AdminBaseController
{
    function __construct(private OrderInitialShowService $orderInitialShowService)
    {
    }

    function __invoke(int $id)
    {
        $order = $this->orderInitialShowService->show($id);

        return $this->successResponse('Order has ben fetched successfully.', $order);
    }
}
