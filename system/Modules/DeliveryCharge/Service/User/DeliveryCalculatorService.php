<?php

namespace Modules\DeliveryCharge\Service\User;

class DeliveryCalculatorService
{
    /**
     * Calculate Delivery Charge - returns a simple flat delivery charge.
     */
    public function calculate($cartData, $addressData, $allDeliveryCharges)
    {
        // Get the first delivery charge from the list
        if (!empty($allDeliveryCharges)) {
            $charge = $allDeliveryCharges[0];
            return [
                'cost' => (float) ($charge['delivery_charge'] ?? 0),
                'type' => 'Flat Rate'
            ];
        }

        // Default to 0 if no delivery charge is set
        return ['cost' => 0, 'type' => 'No Delivery Charge'];
    }
}
