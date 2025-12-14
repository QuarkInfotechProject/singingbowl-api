<?php

namespace Modules\DeliveryCharge\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\DeliveryCharge\App\Models\DeliveryCharge;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class DeliveryChargeUpdateService
{
    function update($data, string $ipAddress)
    {
        $deliveryCharge = DeliveryCharge::find($data['id']);

        if (!$deliveryCharge) {
            throw new Exception('Delivery Charge not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $deliveryCharge->update([
                'description'                => $data['description'],
                'delivery_charge'            => $data['deliveryCharge'],
                'additional_charge_per_item' => $data['additionalChargePerItem'],
                'weight_based_charge'        => $data['weightBasedCharge'],

                // New fields updated here
                'country'                    => $data['country'],
                'country_code'               => $data['countryCode'] ?? null,
                'charge_above_20kg'          => $data['chargeAbove20kg'] ?? 0,
                'charge_above_45kg'          => $data['chargeAbove45kg'] ?? 0,
                'charge_above_100kg'         => $data['chargeAbove100kg'] ?? 0,
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to update DeliveryCharge during transaction: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data'      => $data,
                'ipAddress' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Delivery charge updated for country: ' . $deliveryCharge->country . ' (' . $deliveryCharge->description . ')',
                $deliveryCharge->id,
                ActivityTypeConstant::DELIVERY_CHARGE_UPDATED,
                $ipAddress
            )
        );
    }
}
