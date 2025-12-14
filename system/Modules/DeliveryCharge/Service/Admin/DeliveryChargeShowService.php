<?php

namespace Modules\DeliveryCharge\Service\Admin;

use Modules\DeliveryCharge\App\Models\DeliveryCharge;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class DeliveryChargeShowService
{
    public function show(int $id)
    {
        $deliveryCharge = DeliveryCharge::select(
            'id',
            'description',
            'country', // New Field
            'country_code as countryCode', // New Field
            'delivery_charge as deliveryCharge',
            'additional_charge_per_item as additionalChargePerItem',
            'weight_based_charge as weightBasedCharge',
            // New Weight Fields
            'charge_above_20kg as chargeAbove20kg',
            'charge_above_45kg as chargeAbove45kg',
            'charge_above_100kg as chargeAbove100kg'
        )
        ->find($id);

        if (!$deliveryCharge) {
            throw new Exception('Delivery Charge not found.', ErrorCode::NOT_FOUND);
        }

        return $deliveryCharge;
    }
}
