<?php

namespace Modules\Coupon\Checkers;

use Closure;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class   UsageLimitPerCoupon
{
    public function handle(array $data, Closure $next)
    {
        $coupon = $data['coupon'];

        if ($coupon->usageLimitReached()) {
            throw new Exception("The coupon usage limit has been reached.", ErrorCode::FORBIDDEN);
        }

        return $next($data);
    }
}
