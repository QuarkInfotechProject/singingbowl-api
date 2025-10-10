<?php

namespace Modules\OrderProcessing\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderProcessingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'orders' => ['array', 'min:1'],
            'orders.*' => ['exists:orders,id'],
            'shippingCompany' => ['required', 'in:None,Pathao']
        ];
    }

    public function messages()
    {
        return [
            'orders.array' => 'The orders field must be an array.',
            'orders.min' => 'At least one order is required.',
            'orders.*.exists' => 'The order with ID :input does not exist in the database.',
            'shippingCompany.required' => 'The shipping company field is required.',
            'shippingCompany.in' => 'The shipping company must be either (None or Pathao).',
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
