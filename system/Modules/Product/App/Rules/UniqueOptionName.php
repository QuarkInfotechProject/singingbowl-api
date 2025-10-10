<?php

namespace Modules\Product\App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;

class UniqueOptionName implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $options = $this->getRequest()->input('options', []);

        $sameNameOptions = collect($options)
            ->where('name', $value)
            ->count();

        $imageOptionCount = collect($options)
            ->where('hasImage', 1)
            ->count();

        if ($sameNameOptions > 1) {
            $fail("The '{$value}' option already exists.");
        }

        if ($imageOptionCount > 1) {
            $fail('A product can only contain one option with images.');
        }
    }

    protected function getRequest()
    {
        return app(Request::class);
    }
}
