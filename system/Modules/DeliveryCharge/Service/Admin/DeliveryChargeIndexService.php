<?php

namespace Modules\DeliveryCharge\Service\Admin;

use Modules\DeliveryCharge\App\Models\DeliveryCharge;

class DeliveryChargeIndexService
{
    function index()
    {
        return DeliveryCharge::select(
            'id',
            'description',
            'country',
            'delivery_charge as deliveryCharge',
            // New fields aliased for frontend consistency
            'charge_above_20kg as chargeAbove20kg',
            'charge_above_45kg as chargeAbove45kg',
            'charge_above_100kg as chargeAbove100kg'
        )
        ->get();
    }
}
