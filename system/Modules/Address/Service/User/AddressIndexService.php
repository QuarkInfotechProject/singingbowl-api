<?php

namespace Modules\Address\Service\User;

use Illuminate\Support\Facades\Auth;
use Modules\Address\App\Models\Address;

class AddressIndexService
{
    public function index()
    {
        $userId = Auth::id();

        $address = Address::where('user_id', $userId)
            ->first();

        return [
            'address' => $address ? [
                'uuid' => $address->uuid,
                'userId' => $address->user_id,
                
                // New Fields
                'email' => $address->email,
                'addressLine1' => $address->address_line_1, // Renamed from 'address'
                'addressLine2' => $address->address_line_2,
                'postalCode' => $address->postal_code,
                'landmark' => $address->landmark,
                'addressType' => $address->address_type,
                'deliveryInstructions' => $address->delivery_instructions,
                'isDefault' => (bool) $address->is_default,
                'label' => $address->label,

                // Existing Fields
                'firstName' => $address->first_name,
                'lastName' => $address->last_name,
                'mobile' => $address->mobile,
                'backupMobile' => $address->backup_mobile,
                'countryName' => $address->country_name,
                'provinceId' => $address->province_id,
                'provinceName' => $address->province_name,
                'cityId' => $address->city_id,
                'cityName' => $address->city_name,
                'zoneId' => $address->zone_id,
                'zoneName' => $address->zone_name
            ] : null
        ];
    }
}
