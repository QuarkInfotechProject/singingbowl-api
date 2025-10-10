<?php

namespace Modules\Support\App\Http\Controllers\Admin\OrderSupport;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Support\Service\Admin\OrderSupport\OrderSupportIndexService;

class OrderSupportIndexController extends AdminBaseController
{
    function __construct(private OrderSupportIndexService $orderSupportIndexService)
    {
    }

    function __invoke()
    {
        $support = $this->orderSupportIndexService->index();

        return $this->successResponse('Order support has been fetched successfully.', $support);
    }
}
