<?php

namespace Modules\Coupon\Checkers;

use Closure;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ExcludedCoupons
{
    public function handle($data, Closure $next)
    {
        $coupon = $data['coupon'];
        $cart = $data['cart'];

        $excludedCouponIds = $coupon->excludedCoupons->pluck('id')->toArray();

        if (!empty($excludedCouponIds)) {
            $appliedCoupons = $cart->getAppliedCoupons();
            $appliedCouponIds = $appliedCoupons->pluck('id')->toArray();

            if (array_intersect($excludedCouponIds, $appliedCouponIds)) {
                throw new Exception('This coupon cannot be used with one of the applied coupons.', ErrorCode::UNPROCESSABLE_CONTENT);
            }
        }

        return $next($data);
    }
}
