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
    public function create($data)
    {
        $userId = Auth::user()->id;

        try {
            DB::beginTransaction();

            Address::create([
                'uuid' => Str::uuid(),
                'user_id' => $userId,

                // New Fields
                'email' => $data['email'],
                'address_line_1' => $data['addressLine1'],
                'address_line_2' => $data['addressLine2'] ?? null,
                'postal_code' => $data['postalCode'],
                'landmark' => $data['landmark'] ?? null,
                'address_type' => $data['addressType'] ?? 'home',
                'delivery_instructions' => $data['deliveryInstructions'] ?? null,
                'is_default' => $data['isDefault'] ?? false,
                'label' => $data['label'] ?? null,

                // Existing Fields
                'first_name' => $data['firstName'],
                'last_name' => $data['lastName'],
                'mobile' => $data['mobile'],
                'backup_mobile' => $data['backupMobile'],
                'country_name' => $data['countryName'],
                'country_code' => $data['countryCode'], // Added here
                'province_id' => $data['provinceId'],
                'province_name' => $data['provinceName'],
                'city_id' => $data['cityId'],
                'city_name' => $data['cityName'],
                'zone_id' => $data['zoneId'],
                'zone_name' => $data['zoneName']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            Log::info('Failed to create address.', ['error' => $exception->getMessage(), 'user_id' => $userId]);

            if ($exception->getCode() == 23000) {
                throw new Exception('An address for this user already exists.', ErrorCode::UNPROCESSABLE_CONTENT);
            };

            throw $exception;
        }
    }
}
