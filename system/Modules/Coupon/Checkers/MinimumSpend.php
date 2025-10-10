<?php

namespace Modules\Coupon\Checkers;

use Closure;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class MinimumSpend
{
    public function handle(array $data, Closure $next)
    {
        $coupon = $data['coupon'];
        $cart = $data['cart'];

        // Use the updated didNotSpendTheRequiredAmount method that accepts a cart parameter
        if ($coupon->didNotSpendTheRequiredAmount($cart)) {
            throw new Exception("You need to spend at least {$coupon->minimum_spend} to apply this coupon.", ErrorCode::FORBIDDEN);
        }

        return $next($data);
    }
}
