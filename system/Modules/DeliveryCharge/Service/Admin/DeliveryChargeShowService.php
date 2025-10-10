<?php

namespace Modules\DeliveryCharge\Service\Admin;

use Modules\DeliveryCharge\App\Models\DeliveryCharge;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class DeliveryChargeShowService
{
    public function show(int $id)
    {
        $deliveryCharge = DeliveryCharge::select('id', 'description', 'delivery_charge as deliveryCharge',
            'additional_charge_per_item as additionalChargePerItem', 'weight_based_charge as weightBasedCharge')
            ->find($id);

        if (!$deliveryCharge) {
            throw new Exception('Delivery Charge not found.', ErrorCode::NOT_FOUND);
        }

        return $deliveryCharge;
    }
}
