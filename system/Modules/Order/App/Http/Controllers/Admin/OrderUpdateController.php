<?php

namespace Modules\Order\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Order\App\Http\Requests\OrderUpdateRequest;
use Modules\Order\Service\Admin\OrderUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class OrderUpdateController extends AdminBaseController
{
    function __construct(private OrderUpdateService $orderUpdateService)
    {
    }

    function __invoke(Request $request)
    {
        $this->orderUpdateService->update($request->all());

        return $this->successResponse('Order has been updated successfully.');
    }
}
