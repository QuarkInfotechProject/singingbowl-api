<?php

namespace Modules\Warranty\Service\WarrantyRegistration;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Warranty\App\Models\WarrantyRegistration;

class WarrantyRegistrationCreateService
{
    function create($data)
    {
        try {
            DB::beginTransaction();

            $warranty = WarrantyRegistration::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'product_name' => $data['productName'],
                'quantity' => $data['quantity'],
                'date_of_purchase' => $data['dateOfPurchase'],
                'purchased_from' => $data['purchasedFrom'],
                'order_id' => $data['orderId'],
                'address' => $data['address'],
                'country_name' => $data['countryName'] ?? 'NP',
                'province_name' => $data['provinceName'],
                'city_name' => $data['cityName'],
                'zone_name' => $data['zoneName'],
            ]);

            DB::commit();

            return $warranty->product_name;
        } catch (\Exception $exception) {
            Log::error('Error creating warranty registration: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
