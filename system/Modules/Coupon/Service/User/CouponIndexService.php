<?php

namespace Modules\Coupon\Service\User;

use Illuminate\Support\Facades\DB;
use Modules\Coupon\App\Models\Coupon;

class CouponIndexService
{
    function index()
    {
        return Coupon::select(
            'name',
            'code',
            'value',
           'type',
            'min_quantity as minQuantity',
            'apply_automatically as applyAutomatically',
            DB::raw("DATE_FORMAT(end_date, '%M %d, %Y') as expiryDate"),
            DB::raw("JSON_UNQUOTE(payment_methods) as paymentMethods")
        )
            ->where('is_active', true)
            ->where('is_public', true)
            ->get()
            ->map(function ($coupon) {
                $coupon->paymentMethods = json_decode($coupon->paymentMethods, true);
                return $coupon;
            });
    }
}
