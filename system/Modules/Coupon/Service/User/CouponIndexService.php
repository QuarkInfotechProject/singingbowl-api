<?php

namespace Modules\Coupon\Service\User;

use Carbon\Carbon;
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
            'end_date',
            'payment_methods'
        )
            ->where('is_active', true)
            ->where('is_public', true)
            ->get()
            ->map(function ($coupon) {
                $coupon->expiryDate = $coupon->end_date ? Carbon::parse($coupon->end_date)->format('F d, Y') : null;
                $coupon->paymentMethods = $coupon->payment_methods ?? [];
                $coupon->makeHidden(['end_date', 'payment_methods']);
                return $coupon;
            });
    }
}
