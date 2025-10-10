<?php

namespace Modules\Coupon\Checkers;

use Closure;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ValidCoupon
{
    public function handle(array $data, Closure $next)
    {
        if ($data['coupon']->invalid()) {
            throw new Exception('The coupon is not valid.', ErrorCode::FORBIDDEN);
        }

        return $next($data);
    }
}
