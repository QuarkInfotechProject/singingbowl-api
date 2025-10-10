<?php

namespace Modules\OrderProcessing\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\OrderProcessing\Service\OrderProcessingTrackService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class OrderProcessingTrackController extends AdminBaseController
{
    public function __construct(private OrderProcessingTrackService $orderProcessingTrackService)
    {
    }

    public function __invoke(int $orderId, int $mobile)
    {
        $url = $this->orderProcessingTrackService->trackOrderProcessing($orderId, $mobile);

        return $this->successResponse('Order has been tracked successfully.', $url);
    }
}
