<?php

namespace Modules\OrderProcessing\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\OrderProcessing\Service\OrderIndexForProcessingService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class OrderIndexForProcessingController extends AdminBaseController
{
    function __construct(private OrderIndexForProcessingService $orderIndexForProcessingService)
    {
    }

    function __invoke(Request $request)
    {
        $orders = $this->orderIndexForProcessingService->index($request->all());

        return $this->successResponse('Orders for processing has been fetched successfully.', $orders);
    }
}
