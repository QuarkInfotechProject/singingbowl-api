<?php

namespace Modules\Address\Service\User;

use Illuminate\Support\Facades\Auth;
use Modules\Address\App\Models\Address;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\Shared\Exception\Exception; // Fixed namespace import

class AddressShowService
{
    public function show(string $uuid)
    {
        $userId = Auth::user()->id;

        $address = Address::select(
            // New Fields
            'email',
            'address_line_1 as addressLine1', // Renamed from 'address'
            'address_line_2 as addressLine2',
            'postal_code as postalCode',
            'landmark',
            'address_type as addressType',
            'delivery_instructions as deliveryInstructions',
            'is_default as isDefault',
            'label',

            // Existing Fields
            'first_name as firstName',
            'last_name as lastName',
            'mobile',
            'backup_mobile as backupMobile',
            'country_name as countryName',
            'province_id as provinceId',
            'province_name as provinceName',
            'city_id as cityId',
            'city_name as cityName',
            'zone_id as zoneId',
            'zone_name as zoneName'
        )
        ->where('user_id', $userId)
        ->where('uuid', $uuid)
        ->first();

        if (!$address) {
            throw new Exception('Address not found', ErrorCode::NOT_FOUND);
        }

        return $address;
    }
}
