<?php

namespace Modules\Coupon\Checkers;

use Closure;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class PaymentMethodCheck
{
    public function handle($data, Closure $next)
    {
        $coupon = $data['coupon'];
        $paymentMethod = $data['paymentMethod'];

        $allowedPaymentMethods = $coupon->payment_methods ? json_decode($coupon->payment_methods, true) : [];

        if (!empty($allowedPaymentMethods) && !in_array($paymentMethod, $allowedPaymentMethods)) {
            throw new Exception("Coupon {$coupon->code} is not valid for the selected payment method.", ErrorCode::UNPROCESSABLE_CONTENT);
        }

        return $next($data);
    }
}
