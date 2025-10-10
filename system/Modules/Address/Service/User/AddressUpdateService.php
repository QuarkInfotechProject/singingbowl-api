<?php

namespace Modules\Address\Service\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Address\App\Models\Address;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AddressUpdateService
{
    function update($data)
    {
        $userId = Auth::user()->id;

        $address = Address::where('user_id', $userId)
                    ->first();

        if (!$address) {
            throw new Exception('Address not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $address->update([
                'first_name' => $data['firstName'],
                'last_name' => $data['lastName'],
                'mobile' => $data['mobile'],
                'backup_mobile' => $data['backupMobile'],
                'address' => $data['address'],
                'country_name' => $data['countryName'],
                'province_id' => $data['provinceId'],
                'province_name' => $data['provinceName'],
                'city_id' => $data['cityId'],
                'city_name' => $data['cityName'],
                'zone_id' => $data['zoneId'],
                'zone_name' => $data['zoneName']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::info('Failed to update address.', ['error' => $exception->getMessage(), 'address_id' => $address->id]);
            DB::rollBack();
            throw $exception;
        }
    }
}
