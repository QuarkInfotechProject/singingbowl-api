<?php

namespace Modules\DeliveryCharge\App\Http\Controllers\Admin;

use Modules\DeliveryCharge\Service\Admin\DeliveryChargeShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class DeliveryChargeShowController extends AdminBaseController
{
    function __construct(private DeliveryChargeShowService $deliveryChargeShowService)
    {
    }

    function __invoke(int $id)
    {
        $attributeSet = $this->deliveryChargeShowService->show($id);

        return $this->successResponse('Delivery charge has been fetched successfully.', $attributeSet);
    }
}
