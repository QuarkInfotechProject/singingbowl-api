<?php

namespace Modules\Coupon\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Coupon\App\Models\Coupon;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CouponDestroyService
{
    function destroy(int $id, string $ipAddress)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            throw new Exception('Coupon not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $coupon->delete();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Coupon destroyed of code: ' . $coupon->code,
                $coupon->id,
                ActivityTypeConstant::COUPON_DESTROYED,
                $ipAddress
            )
        );
    }
}
