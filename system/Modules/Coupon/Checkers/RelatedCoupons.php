<?php

namespace Modules\Coupon\Checkers;

use Closure;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class RelatedCoupons
{
    public function handle($data, Closure $next)
    {
        $coupon = $data['coupon'];
        $cart = $data['cart'];

        $combinableCouponIds = $coupon->combinableCoupons->pluck('id')->toArray();

        $appliedCoupons = $cart->getAppliedCoupons();
        $appliedCouponIds = $appliedCoupons->pluck('id')->toArray();

        foreach ($appliedCouponIds as $appliedCouponId) {

            if ($appliedCouponId === $coupon->id) {
                continue;
            }

            // If no specific combinable coupons are defined, exclude by default
            // If combinable coupons are defined, only allow those specific coupons
            if (empty($combinableCouponIds) || !in_array($appliedCouponId, $combinableCouponIds)) {
                throw new Exception('This coupon cannot be combined with the already applied coupons.', ErrorCode::UNPROCESSABLE_CONTENT);
            }
        }

        return $next($data);
    }
}
