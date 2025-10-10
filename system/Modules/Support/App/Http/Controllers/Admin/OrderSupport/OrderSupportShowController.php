<?php

namespace Modules\Support\App\Http\Controllers\Admin\OrderSupport;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Support\Service\Admin\OrderSupport\OrderSupportShowService;

class OrderSupportShowController extends AdminBaseController
{
    function __construct(private OrderSupportShowService $orderSupportShowService)
    {
    }

    function __invoke(int $id)
    {
        $support = $this->orderSupportShowService->show($id);

        return $this->successResponse('Order support has been fetched successfully.', $support);
    }
}
