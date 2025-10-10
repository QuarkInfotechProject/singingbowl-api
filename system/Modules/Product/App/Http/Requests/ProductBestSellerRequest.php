<?php

namespace Modules\Product\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductBestSellerRequest extends FormRequest
{
    public function rules()
    {
        return [
            'Id' => 'required|string|exists:products,uuid',
            'bestSeller' => 'required|boolean'
        ];
    }
    public function messages()
    {
        return [
            'Id.required' => 'Product ID is required.',
            'Id.string' => 'Product ID must be a string.',
            'Id.exists' => 'The selected product does not exist.',
            'bestSeller.required' => 'BestSeller status is required.',
            'bestSeller.boolean' => 'BestSeller status must be true or false.'
        ];
    }
}