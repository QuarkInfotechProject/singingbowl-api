<?php

namespace Modules\DeliveryCharge\App\Http\Controllers\Admin;

use Modules\DeliveryCharge\App\Http\Requests\DeliveryChargeUpdateRequest;
use Modules\DeliveryCharge\Service\Admin\DeliveryChargeUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class DeliveryChargeUpdateController extends AdminBaseController
{
    function __construct(private DeliveryChargeUpdateService $deliveryChargeUpdateService)
    {
    }

    function __invoke(DeliveryChargeUpdateRequest $request)
    {
        $this->deliveryChargeUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('Delivery charge has been updated successfully.');
    }
}
