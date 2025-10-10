<?php

namespace Modules\Coupon\Service\Admin;

use Modules\Coupon\App\Models\Coupon;

class CouponShowNameService
{
    function index()
    {
        $query = Coupon::query();

        $result = $query->select('id', 'code')
            ->where('is_bulk_offer', true)
            ->latest('created_at')
            ->get();

        return $result ?? collect([]);
    }
}
