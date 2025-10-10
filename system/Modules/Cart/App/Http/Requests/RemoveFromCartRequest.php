<?php

namespace Modules\Cart\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemoveFromCartRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'cartId' => ['required', 'string', 'exists:carts,uuid'],
            'id' => ['required', 'integer', 'exists:cart_items,id'],
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