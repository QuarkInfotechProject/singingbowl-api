<?php

namespace Modules\User\Service\Admin;

use Modules\Address\App\Models\Address;
use Modules\Order\App\Models\OrderAddress;
use Modules\User\App\Models\User;

class UserShowService
{
    function show(string $uuid)
    {
        $user = User::where('uuid', $uuid)->first();

        $billingAddress = [];

        $orderAddress = Address::where('user_id', $user->id)->first();

        if ($orderAddress) {
            $billingAddress[] = [
                'firstName' => $orderAddress->first_name,
                'lastName' => $orderAddress->last_name,
                'mobile' => $orderAddress->mobile,
                'backupMobile' => $orderAddress->backup_mobile,
                'address' => $orderAddress->address,
                'countryName' => $orderAddress->country_name,
                'provinceId' => $orderAddress->province_id,
                'provinceName' => $orderAddress->province_name,
                'cityId' => $orderAddress->city_id,
                'cityName' => $orderAddress->city_name,
                'zoneId' => $orderAddress->zone_id,
                'zoneName' => $orderAddress->zone_name,
            ];
        }

        return [
            'fullName' => $user->full_name,
            'email' => $user->email,
            'phoneNo' => $user->phone_no,
            'dateOfBirth' => $user->date_of_birth,
            'gender' => $user->gender,
            'offersNotification' => $user->offer_notification,
            'profilePicture' => $user->profile_picture,
            'status' => User::$userStatus[$user->status],
            'remarks' => $user->remarks,
            'billingAddress' => $billingAddress,
        ];
    }
}
