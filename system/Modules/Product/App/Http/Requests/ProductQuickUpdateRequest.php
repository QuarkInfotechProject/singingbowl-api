<?php

namespace Modules\Product\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductQuickUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'uuid' => 'required|string',
            'originalPrice' => 'numeric|min:1',
            'specialPrice' => 'nullable|numeric|min:0|lte:originalPrice',
            'specialPriceStart' => 'nullable|date|after_or_equal:today|required_with:specialPrice',
            'specialPriceEnd' => 'nullable|date|after:specialPriceStart|required_with:specialPriceStart',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'inStock' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'uuid.required' => 'Please provide the product uuid.',

            'originalPrice.required' => 'Please specify the original price of the product.',
            'originalPrice.numeric' => 'The original price must be a number.',
            'originalPrice.min' => 'The original price must be at least :min.',
            'specialPrice.numeric' => 'The special price must be a number.',
            'specialPrice.min' => 'The special price must be at least :min.',
            'specialPrice.lte' => 'The special price must be less than or equal to the original price.',
            'specialPriceStart.date' => 'The start date of the special price must be a valid date.',
            'specialPriceStart.after_or_equal' => 'The start date of the special price must be today or in the future.',
            'specialPriceStart.required_with' => 'The start date of the special price is required if a special price is set.',
            'specialPriceEnd.date' => 'The end date of the special price must be a valid date.',
            'specialPriceEnd.after' => 'The end date of the special price must be after the start date.',
            'specialPriceEnd.required_with' => 'The end date of the special price is required if a special price is set.',

            'status.required' => 'Please specify the status of the product.',
            'status.boolean' => 'The status must be either true or false.',

            'quantity.required' => 'Please specify the quantity of the product.',
            'quantity.integer' => 'The quantity must be a whole number.',
            'quantity.min' => 'The quantity must be at least :min.',
            'inStock.required' => 'Please specify if the product is in stock.',
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
