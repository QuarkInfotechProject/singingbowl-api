<?php

namespace Modules\Product\App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;
use Modules\Product\App\Models\ProductVariant;

class UniqueSkuRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $incomingSkus = $this->getRequest()->input('variants.*.sku', []);

        $duplicatedSkus = array_keys(array_filter(array_count_values($incomingSkus), function($count) {
            return $count > 1;
        }));

        if (in_array($value, $duplicatedSkus)) {
            $fail("The SKU '$value' is duplicated in the request. Each SKU must be unique.");
        }

        if (ProductVariant::where('sku', $value)->exists()) {
            $fail("The SKU '$value' already exists.");
        }
    }

    protected function getRequest()
    {
        return app(Request::class);
    }
}
