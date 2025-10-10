<?php

namespace Modules\DeliveryCharge\Service\Admin;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\DeliveryCharge\App\Models\DeliveryCharge;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class DeliveryChargeDestroyService
{
    function destroy(int $id, string $ipAddress)
    {
        $deliveryCharge = DeliveryCharge::find($id);

        if (!$deliveryCharge) {
            throw new Exception('Delivery Charge not found.', ErrorCode::NOT_FOUND);
        }

        try {
            Event::dispatch(
                new AdminUserActivityLogEvent(
                    'Delivery charge destroyed of description: ' . $deliveryCharge->description,
                    $deliveryCharge->id,
                    ActivityTypeConstant::DELIVERY_CHARGE_DESTROYED,
                    $ipAddress
                )
            );

            $deliveryCharge->delete();
        } catch (\Exception $exception) {
            Log::error('Failed to destroy DeliveryCharge: ' . $exception->getMessage(), [
                'exception' => $exception,
                'id' => $id,
                'ipAddress' => $ipAddress
            ]);
            throw $exception;
        }
    }
}
