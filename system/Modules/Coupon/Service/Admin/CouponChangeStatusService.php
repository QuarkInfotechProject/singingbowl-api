<?php

namespace Modules\Coupon\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Coupon\App\Models\Coupon;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CouponChangeStatusService
{
    function changeStatus(int $id)
    {
        try {
            DB::beginTransaction();

            $coupon = Coupon::find($id);

            if (!$coupon) {
                throw new Exception('Coupon not found.', ErrorCode::NOT_FOUND);
            }

            $coupon->update(['is_active' => !$coupon['is_active']]);
            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to change status for Coupon during transaction: ' . $exception->getMessage(), [
                'exception' => $exception,
                'id' => $id
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
