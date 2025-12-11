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
                // Existing fields
                'description'                => $data['description'],
                'delivery_charge'            => $data['deliveryCharge'] ?? 0,
                'additional_charge_per_item' => $data['additionalChargePerItem'] ?? 0,
                'weight_based_charge'        => $data['weightBasedCharge'] ?? 0,

                // New fields with proper naming (snake_case)
                'country'                    => $data['country'] ?? null,
                'country_code'               => $data['countryCode'] ?? null,
                'charge_above_20kg'          => $data['chargeAbove20kg'] ?? 0,
                'charge_above_45kg'          => $data['chargeAbove45kg'] ?? 0,
                'charge_above_100kg'         => $data['chargeAbove100kg'] ?? 0,
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to create DeliveryCharge during transaction: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data'      => $data,
                'ipAddress' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Delivery charge created for country: ' . $deliveryCharge->country . ' (' . $deliveryCharge->description . ')',
                $deliveryCharge->id,
                ActivityTypeConstant::DELIVERY_CHARGE_CREATED,
                $ipAddress
            )
        );
    }
}
