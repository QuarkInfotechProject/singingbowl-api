<?php

namespace Modules\DeliveryCharge\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\DeliveryCharge\App\Models\DeliveryCharge;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class DeliveryChargeCreateService
{
    function create($data, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $deliveryCharge = DeliveryCharge::create([
                'description' => $data['description'],
                'delivery_charge' => $data['deliveryCharge'] ?? 0,
                'additional_charge_per_item' => $data['additionalChargePerItem'],
                'weight_based_charge' => $data['weightBasedCharge']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to create DeliveryCharge during transaction: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data,
                'ipAddress' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Delivery charge created of description: ' . $deliveryCharge->description,
                $deliveryCharge->id,
                ActivityTypeConstant::DELIVERY_CHARGE_CREATED,
                $ipAddress
            )
        );
    }
}
