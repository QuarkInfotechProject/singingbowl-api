<?php

namespace Modules\DeliveryCharge\Service\User;

class DeliveryCalculatorService
{
    /**
     * Calculate Delivery Charge based on country code matching and weight tiers.
     * Returns $9 base price if no matching delivery charge is found.
     */
    public function calculate($cartData, $addressData, $allDeliveryCharges)
    {
        // 1. Get Inputs
        $totalWeight = $cartData['total_weight'] ?? 0; // in Grams
        $countryCode = $addressData['countryCode'] ?? null;
        $countryName = $addressData['countryName'] ?? null;

        // 2. Find the Rate Row for this Country
        // First try to match by country_code, then fallback to country name
        $rateRow = null;
        
        // Try matching by country_code first
        if (!empty($countryCode)) {
            foreach ($allDeliveryCharges as $charge) {
                if (isset($charge['country_code']) && $charge['country_code'] === $countryCode) {
                    $rateRow = $charge;
                    break;
                }
            }
        }
        
        // If no match by code, try matching by country name
        if (!$rateRow && !empty($countryName)) {
            foreach ($allDeliveryCharges as $charge) {
                if (isset($charge['country']) && strcasecmp($charge['country'], $countryName) === 0) {
                    $rateRow = $charge;
                    break;
                }
            }
        }

        // If no rate found for this country, return $9 base price
        if (!$rateRow) {
            return ['cost' => 9.00, 'type' => 'Base Rate (No Country Match)'];
        }

        // 3. Logic based on weight tiers
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

