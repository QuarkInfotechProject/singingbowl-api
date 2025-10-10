<?php

namespace Modules\Coupon\Checkers;

use Closure;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class UsageLimitPerCustomer
{
    public function handle(array $data, Closure $next)
    {
        if (!auth()->guard('user')->check()) {
            return $next($data);
        }

        $coupon = $data['coupon'];
        $user = auth()->guard('user')->user();

        if ($coupon->usage_limit_per_customer && $user->timesUsedCoupon($coupon) >= $coupon->usage_limit_per_customer) {
            throw new Exception('You have reached the usage limit for this coupon.', ErrorCode::BAD_REQUEST);
        }

        return $next($data);
    }
}
