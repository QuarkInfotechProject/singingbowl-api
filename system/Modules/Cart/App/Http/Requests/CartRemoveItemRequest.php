<?php

namespace Modules\Cart\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartRemoveItemRequest extends FormRequest
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
            ];
        } else {
        return [
                'cartId' => 'required|exists:guest_carts,guest_token',
                'id' => 'required|exists:guest_cart_items,id',
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
}