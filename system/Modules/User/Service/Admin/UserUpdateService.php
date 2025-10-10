<?php

namespace Modules\User\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\User\App\Models\User;

class UserUpdateService
{
    function update($data)
    {
        $user = User::with('address')->where('uuid', $data['id'])->first();

        if (!$user) {
            throw new Exception('User not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $user->update([
                'full_name' => $data['fullName'],
                'email' => $data['email'],
                'phone_no' => $data['phoneNo'],
                'date_of_birth' => $data['dateOfBirth'],
                'gender' => $data['gender'],
                'offers_notification' => $data['offersNotification'],
            ]);

            if (!empty($data['billingAddress'])) {
                $address = $user->address()->firstOrNew(['user_id' => $user->id]); // Get existing or create a new instance

                if (!$address->exists) {
                    $address->uuid = Str::uuid();
                }

                $address->fill([
                    'first_name' => $data['billingAddress']['firstName'],
                    'last_name' => $data['billingAddress']['lastName'],
                    'mobile' => $data['billingAddress']['mobile'],
                    'backup_mobile' => $data['billingAddress']['backupMobile'],
                    'address' => $data['billingAddress']['address'],
                    'country_name' => $data['billingAddress']['countryName'],
                    'province_id' => $data['billingAddress']['provinceId'],
                    'province_name' => $data['billingAddress']['provinceName'],
                    'city_id' => $data['billingAddress']['cityId'],
                    'city_name' => $data['billingAddress']['cityName'],
                    'zone_id' => $data['billingAddress']['zoneId'],
                    'zone_name' => $data['billingAddress']['zoneName'],
                ]);

                $address->save();
            }

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error updating user' . $exception->getMessage(), [
                'exception' => $exception,
                'userId' => $user->id
            ]);

            DB::rollBack();
            throw $exception;
        }
    }
}
