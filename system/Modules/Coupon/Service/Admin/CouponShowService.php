<?php

namespace Modules\Coupon\Service\Admin;

use Modules\Coupon\App\Models\Coupon;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CouponShowService
{
    function show(int $id)
    {
        $coupon = Coupon::select(
            'id',
            'name',
            'code',
            'value',
            'max_discount as maxDiscount',
            'type',
            'minimum_spend as minimumSpend',
            'usage_limit_per_coupon as usageLimitPerCoupon',
            'usage_limit_per_customer as usageLimitPerCustomer',
            'min_quantity as minQuantity',
            'is_active as isActive',
            'is_public as isPublic',
            'is_bulk_offer as isBulkOffer',
            'start_date as startDate',
            'end_date as endDate',
            'apply_automatically as applyAutomatically',
            'individual_use_only as individualUse',
            'payment_methods')->find($id);

        if (!$coupon) {
            throw new Exception('Coupon not found.', ErrorCode::NOT_FOUND);
        }

        $coupon->paymentMethods = is_string($coupon->payment_methods)
            ? json_decode($coupon->payment_methods, true)
            : $coupon->payment_methods;

        $coupon->makeHidden(['combinableCoupons', 'excludedCoupons', 'payment_methods']);

        $couponProduct = $coupon->getIncludedAndExcludedProducts();
        $couponRelations = $coupon->getIncludedAndExcludedCoupons();

        return array_merge(
            $coupon->toArray(),
            $couponProduct,
            $couponRelations
        );
    }
}
