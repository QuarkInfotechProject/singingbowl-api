<?php

namespace Modules\DeliveryCharge\App\Http\Controllers\Admin;

use Modules\DeliveryCharge\App\Http\Requests\DeliveryChargeCreateRequest;
use Modules\DeliveryCharge\Service\Admin\DeliveryChargeCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class DeliveryChargeCreateController extends AdminBaseController
{
    function __construct(private DeliveryChargeCreateService $deliveryChargeCreateService)
    {
    }

    function __invoke(DeliveryChargeCreateRequest $request)
    {
        $this->deliveryChargeCreateService->create($request->all(), $request->getClientIp());

        return $this->successResponse('Delivery charge has been created successfully.');
    }
}
