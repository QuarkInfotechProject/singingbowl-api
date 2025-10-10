<?php

namespace Modules\Cart\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuestCartRemoveItemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'cartId' => ['required', 'string', 'exists:guest_carts,guest_token'],
            'id' => ['required', 'integer', 'exists:guest_cart_items,id'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cartId.required' => 'Cart ID is required.',
            'cartId.exists' => 'The specified cart does not exist.',
            'id.required' => 'Item ID is required.',
            'id.exists' => 'The specified item does not exist in the cart.',
        ];
    }
}