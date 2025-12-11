<?php

namespace Modules\DeliveryCharge\Service\User;

use Modules\DeliveryCharge\App\Models\DeliveryCharge;

class DeliveryChargeIndexService
{
    function index()
    {
        return DeliveryCharge::select(
            'id', 
            'description', 
            'country',
            'country_code as countryCode',
            'delivery_charge as deliveryCharge',
            'additional_charge_per_item as additionalChargePerItem',
            'weight_based_charge as weightBasedCharge',
            'charge_above_20kg as chargeAbove20kg',
            'charge_above_45kg as chargeAbove45kg',
            'charge_above_100kg as chargeAbove100kg'
        )
        ->get();
    }
}
