<?php

namespace Modules\DeliveryCharge\Service\User;

class DeliveryCalculatorService
{
    /**
     * Calculate Delivery Charge based on your specific table columns.
     */
    public function calculate($cartData, $addressData, $allDeliveryCharges)
    {
        // 1. Get Inputs
        $totalWeight = $cartData['total_weight'] ?? 0; // in Grams
        $countryCode = $addressData['countryCode'] ?? 'US';

        // 2. Find the Rate Row for this Country
        // We filter the DB results to find the one matching the user's country_code
        $rateRow = null;
        foreach ($allDeliveryCharges as $charge) {
            if (isset($charge['country_code']) && $charge['country_code'] === $countryCode) {
                $rateRow = $charge;
                break;
            }
        }

        // If no rate found for this country, return 0 or default
        if (!$rateRow) {
            return ['cost' => 0, 'type' => 'No Rate Found for Country'];
        }

        // 3. Logic based on your specific columns
        // 20 KG = 20000 Grams
        // 45 KG = 45000 Grams
        // 100 KG = 100000 Grams

        if ($totalWeight >= 100000) {
            // Case: Above 100kg
            return [
                'cost' => (float) $rateRow['charge_above_100kg'],
                'type' => 'Bulk Shipping (>100kg)'
            ];
        } elseif ($totalWeight >= 45000) {
            // Case: 45kg - 99.9kg
            return [
                'cost' => (float) $rateRow['charge_above_45kg'],
                'type' => 'Heavy Shipping (>45kg)'
            ];
        } elseif ($totalWeight >= 20000) {
            // Case: 20kg - 44.9kg
            return [
                'cost' => (float) $rateRow['charge_above_20kg'],
                'type' => 'Medium Shipping (>20kg)'
            ];
        } else {
            // Case: Less than 20kg (Base Charge)
            return [
                'cost' => (float) $rateRow['delivery_charge'],
                'type' => 'Standard Shipping'
            ];
        }
    }
}
