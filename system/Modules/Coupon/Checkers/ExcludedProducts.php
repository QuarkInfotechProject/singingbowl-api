<?php

namespace Modules\Coupon\Checkers;

use Closure;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\App\Models\Cart;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ExcludedProducts
{
    public function handle(array $data, Closure $next)
    {
        $coupon = $data['coupon'];
        $cart = $data['cart'];
        $coupon->load('exclude');

        if ($coupon->exclude->isEmpty()) {
            return $next($data);
        }

        $excludedProducts = $cart->hasAnyProduct($coupon->exclude);

        if ($excludedProducts->isNotEmpty()) {
            $excludedProductNames = $excludedProducts->pluck('product_name')->toArray();
            $excludedProductList = implode(', ', $excludedProductNames);

            throw new Exception(
                "This coupon is not applicable to the following product(s) in your cart: $excludedProductList.",
                ErrorCode::UNPROCESSABLE_CONTENT
            );
        }

        return $next($data);
    }
}
