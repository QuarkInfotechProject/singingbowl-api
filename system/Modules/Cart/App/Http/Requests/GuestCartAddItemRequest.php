<?php

namespace Modules\Cart\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuestCartAddItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Guest cart operations are generally open, specific checks for token are in controller
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'products'            => ['required', 'array', 'min:1'],
            'products.*.productId' => ['required', 'uuid', 'exists:products,uuid'], // Assuming product identifier is UUID
            'products.*.quantity'  => ['required', 'integer', 'min:1'],
            'products.*.variantUuid' => [
                'nullable',
                'uuid',
                'exists:product_variants,uuid'
                // We'll need a more complex rule or handle this validation in the service
                // if we need to ensure variantUuid belongs to productId here.
            ],
            // 'variant_options' might not be needed if we use variantUuid
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'products.required' => 'The products field is required.',
            'products.array' => 'The products must be an array.',
            'products.min' => 'At least one product must be added.',
            'products.*.productId.required' => 'The product ID is required for each item.',
            'products.*.productId.uuid' => 'The product ID must be a valid UUID for each item.',
            'products.*.productId.exists' => 'The selected product ID is invalid for one or more items.',
            'products.*.quantity.required' => 'Quantity is required for each item.',
            'products.*.quantity.integer' => 'Quantity must be an integer for each item.',
            'products.*.quantity.min' => 'Quantity must be at least 1 for each item.',
            'products.*.variantUuid.uuid' => 'The variant UUID must be a valid UUID for each item.',
            'products.*.variantUuid.exists' => 'The selected variant UUID is invalid for one or more items.',
        ];
    }
}