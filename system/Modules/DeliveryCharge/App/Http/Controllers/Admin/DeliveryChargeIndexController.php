<?php

namespace Modules\DeliveryCharge\App\Http\Controllers\Admin;

use Modules\DeliveryCharge\Service\Admin\DeliveryChargeIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class DeliveryChargeIndexController extends AdminBaseController
{
    function __construct(private DeliveryChargeIndexService $deliveryChargeIndexService)
    {
    }

    function __invoke()
    {
        $deliveryCharges = $this->deliveryChargeIndexService->index();

        return $this->successResponse('Delivery charges has been fetched successfully.', $deliveryCharges);
    }
}
