<?php

namespace Modules\Order\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Order\App\Models\Order;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class OrderUpdateService
{
    function update(array $data)
    {
        $order = Order::find($data['orderId']);

        if (!$order) {
            throw new Exception('Order not found.', ErrorCode::NOT_FOUND);
        }

        $orderAddress = $order->orderAddress;

        if (!$orderAddress) {
            throw new Exception('Order address not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $orderAddress->address->update([
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

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error updating order' . $exception->getMessage(), [
                'exception' => $exception,
                'orderId' => $order->id
            ]);

            DB::rollBack();
            throw $exception;
        }
    }
}
