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
            'delivery_charge as deliveryCharge'
        )
        ->get();
    }
}
