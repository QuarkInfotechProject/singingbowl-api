<?php

namespace Modules\Product\App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class WithoutSpacesRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (str_contains($value, ' ')) {
            $fail("The SKU '$value' should not contain any white spaces.");
        }
    }
}
