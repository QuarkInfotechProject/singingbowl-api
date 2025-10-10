<?php

namespace Modules\Coupon\Checkers;

use Closure;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\App\Models\Cart;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ApplicableProducts
{
    public function handle(array $data, Closure $next)
    {
        $coupon = $data['coupon'];
        $cart = $data['cart'];
        $coupon->load('products');

        if ($coupon->products->isEmpty()) {
            return $next($data);
        }

        $includedProducts = $cart->hasAnyProduct($coupon->products);

        if ($includedProducts->isNotEmpty()) {
            $includedProductNames = $includedProducts->pluck('product_name')->toArray();
            $includedProductsList = implode(', ', $includedProductNames);

            throw new Exception(
                "This coupon is only applicable to the following product(s) in your cart: $includedProductsList.",
                ErrorCode::UNPROCESSABLE_CONTENT
            );
        }

        return $next($data);
    }
}
