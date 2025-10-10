<?php

namespace Modules\Address\Service\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Address\App\Models\Address;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AddressCreateService
{
    function create($data)
    {
        $userId = Auth::user()->id;

        try {
            DB::beginTransaction();

            Address::create([
                'uuid' => Str::uuid(),
                'user_id' => $userId,
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
            Log::info('Failed to create address.', ['error' => $exception->getMessage(), 'user_id' => $userId]);

            if ($exception->getCode() == 23000) {
                throw new Exception('An address for this user already exists.', ErrorCode::UNPROCESSABLE_CONTENT);
            };

            DB::rollBack();
            throw $exception;
        }
    }
}
