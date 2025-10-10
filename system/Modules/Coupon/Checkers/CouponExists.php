<?php

namespace Modules\Coupon\Checkers;

use Closure;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CouponExists
{
    public function handle(array $data, Closure $next)
    {
        if (!$data['coupon']) {
            throw new Exception("The coupon does not exist.", ErrorCode::NOT_FOUND);
        }

        return $next($data);
    }
}
