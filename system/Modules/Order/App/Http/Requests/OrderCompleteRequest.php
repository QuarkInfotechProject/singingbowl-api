<?php

namespace Modules\Order\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Order\App\Rules\ValidPaymentMethod;

class OrderCompleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'orderId' => 'required|integer',
            'paymentMethod' => ['required', 'string', new ValidPaymentMethod],
            'token' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'orderId.required' => 'Order id is required.',
            'orderId.integer' => 'Order id must be an integer.',
            'paymentMethod.required' => 'Please select a valid payment method.',
            'token.required' => 'Token is required.',
            'token' => 'Token must be of type string.'
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
     * Merge route parameters into request data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'orderId'       => $this->route('orderId') ?? $this->orderId,
            'paymentMethod' => $this->route('paymentMethod') ?? $this->paymentMethod,
        ]);
    }
}
