<?php

namespace Modules\Coupon\Service\Admin;

use Illuminate\Support\Facades\DB;
use Modules\Coupon\App\Models\Coupon;

class CouponIndexService
{
    function index(?string $couponCode = null)
    {
        $query = Coupon::query();

        $query->when(true, function ($query) use ($couponCode) {
            return $query->where(function ($query) use ($couponCode) {
                $query->where('code', 'like', '%' . $couponCode . '%')
                    ->orWhere('name', 'like', '%' . $couponCode . '%');
            });
        });

        $result =  $query->select(
            'id',
            'name as description',
            'code',
            'value as couponAmount',
            'type',
            'used as usage',
            'usage_limit_per_coupon as limit',
            DB::raw("DATE_FORMAT(end_date, '%M %d, %Y') as expiryDate"),
            'is_active as isActive'
        )
            ->latest('created_at')
            ->paginate(25);

        return $result ?? collect([]);
    }
}
