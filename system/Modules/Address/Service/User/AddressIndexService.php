<?php

namespace Modules\Address\Service\User;

use Illuminate\Support\Facades\Auth;
use Modules\Address\App\Models\Address;
use Modules\Shared\Exception\Exception;

class AddressIndexService
{
    function index()
    {
        $userId = Auth::id();

        $address = Address::where('user_id', $userId)
            ->first();

        return [
            'address' => $address ? [
                'uuid' => $address->uuid,
                'userId' => $address->user_id,
                'firstName' => $address->first_name,
                'lastName' => $address->last_name,
                'mobile' => $address->mobile,
                'backupMobile' => $address->backup_mobile,
                'address' => $address->address,
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
