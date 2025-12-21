<?php

namespace Modules\Order\App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPaymentMethod implements ValidationRule
{
    protected $validMethods = [
        // 'esewa',
        // 'IMEPay',
        // 'khalti',
        // 'card',
        'cod',
        'getPay'
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!in_array($value, $this->validMethods)) {
            $fail("The selected {$attribute} is not a valid payment method.");
        }
    }
}
