<?php

namespace Modules\DeliveryCharge\App\Http\Controllers\User;

use Modules\DeliveryCharge\Service\User\DeliveryChargeIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class DeliveryChargeIndexController extends UserBaseController
{
    public function __construct(private DeliveryChargeIndexService $deliveryChargeIndexService)
    {
    }

    public function __invoke()
    {
        $deliveryCharges = $this->deliveryChargeIndexService->index();

        return $this->successResponse('Delivery charges has been fetched successfully.', $deliveryCharges);
    }
}
