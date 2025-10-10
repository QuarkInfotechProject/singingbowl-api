<?php

namespace Modules\Product\App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;

class UniqueOptionValue implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $options = $this->getRequest()->input('options', []);
        $optionIndex = (int) explode('.', $attribute)[1];

        $sameNameValues = collect($options[$optionIndex]['values'])
            ->where('optionName', $value)
            ->count();

        if ($sameNameValues > 1) {
            $fail('The option value already exists.');
        }
    }

    protected function getRequest()
    {
        return app(Request::class);
    }
}
