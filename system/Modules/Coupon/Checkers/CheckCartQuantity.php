<?php

namespace Modules\Coupon\Checkers;

use Closure;
use Modules\Cart\App\Models\Cart;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CheckCartQuantity
{
    public function handle(array $data, Closure $next)
    {
        $coupon = $data['coupon'];
        $cart = $data['cart'];

        if ($coupon->min_quantity && $cart->totalItems() < $coupon->min_quantity) {
            throw new Exception("Minimum quantity for this coupon: {$coupon->min_quantity} has not been met.", ErrorCode::UNPROCESSABLE_CONTENT);
        }

        return $next($data);
    }
}
