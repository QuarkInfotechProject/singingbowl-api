<?php

namespace Modules\Product\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Product\App\Rules\UniqueOptionName;
use Modules\Product\App\Rules\UniqueOptionValue;

class ProductVariantUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'productUuid' => 'required|string',
            'variantUuid' => 'required|string',
            'status' => 'required|boolean',
            'originalPrice' => 'required|numeric|min:0',
            'specialPrice' => 'nullable|numeric|min:0|lte:originalPrice',
            'specialPriceStart' => 'nullable|date|after_or_equal:today',
            'specialPriceEnd' => 'nullable|date|after:specialPriceStart|required_with:specialPriceStart',
            'quantity' => 'required|integer|min:0',
            'inStock' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'productUuid' => 'The UUID field is required and must be a string.',
            'variantUuid' => 'The variant UUID field is required and must be a string.',
            'status.required' => 'The status field is required.',
            'status.boolean' => 'The status field must be either true or false.',
            'originalPrice.required' => 'The original price field is required.',
            'originalPrice.numeric' => 'The original price must be a number.',
            'originalPrice.min' => 'The original price must be at least 0.',
            'specialPrice.numeric' => 'The special price must be a number.',
            'specialPrice.min' => 'The special price must be at least 0.',
            'specialPrice.lte' => 'The special price must be less than or equal to the original price.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.integer' => 'The quantity must be an integer.',
            'quantity.min' => 'The quantity must be at least 0.',
            'inStock.required' => 'The in stock field is required.',
            'inStock.boolean' => 'The in stock field must be either true or false.',
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
