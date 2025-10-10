<?php

namespace Modules\DeliveryCharge\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\DeliveryCharge\Service\Admin\DeliveryChargeDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class DeliveryChargeDestroyController extends AdminBaseController
{
    function __construct(private DeliveryChargeDestroyService $deliveryChargeDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->deliveryChargeDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('Delivery charge has been destroyed successfully.');
    }
}
