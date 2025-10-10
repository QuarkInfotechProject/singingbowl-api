<?php

namespace Modules\Cart\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartUpdateItemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $cartType = $this->get('cart_type');

        if ($cartType === 'user') {
            return [
                'cartId' => 'required|exists:carts,uuid',
                'id' => 'required|exists:cart_items,id',
                'quantity' => 'required|integer|min:1',
            ];
        } else {
        return [
                'cartId' => 'required|exists:guest_carts,guest_token',
                'id' => 'required|exists:guest_cart_items,id',
            'quantity' => 'required|integer|min:1',
        ];
        }
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation()
    {
        // No transformation needed as we're validating the actual field names used in the request
    }

    /**
     * Get validated data.
     */
    public function validatedWithTransformedKeys()
    {
        return $this->validated();
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
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be an integer.',
            'quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}