<?php

namespace Modules\Coupon\Checkers;

use Closure;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class StackableCoupon
{
    public function handle($data, Closure $next)
    {
        $coupon = $data['coupon'];
        $cart = $data['cart'];

        if ($coupon->individual_use_only) {
            if ($cart->hasAppliedCoupons()) {
                $cart->removeAllCoupons();
            }
        } else {
            $appliedCoupons = $cart->getAppliedCoupons();
            foreach ($appliedCoupons as $appliedCoupon) {
                if ($appliedCoupon->individual_use_only) {
                    throw new Exception('Cannot apply this coupon. An exclusive coupon is already applied.', ErrorCode::UNPROCESSABLE_CONTENT);
                }
            }
        }

        return $next($data);
    }
}
