<?php

namespace Modules\OrderProcessing\App\Http\Controllers;

use Modules\OrderProcessing\App\Http\Requests\RefundProcessRequest;
use Modules\OrderProcessing\Service\RefundProcessService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ProcessRefundController extends AdminBaseController
{
    function __construct(private RefundProcessService $refundProcessService)
    {
    }

    function __invoke(RefundProcessRequest $request, $orderId)
    {
        $this->refundProcessService->refundProcess($request->all(), $orderId);

        return $this->successResponse('Refund process carried out successfully.');
    }
}
