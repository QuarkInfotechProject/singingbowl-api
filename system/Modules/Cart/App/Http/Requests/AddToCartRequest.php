<?php

namespace Modules\Cart\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'products' => ['required', 'array'],
            'products.*.productId' => ['required', 'uuid', 'exists:products,uuid'],
            'products.*.variantUuid' => ['nullable', 'uuid', 'exists:product_variants,uuid'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
