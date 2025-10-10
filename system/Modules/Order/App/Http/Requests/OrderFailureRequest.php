<?php

namespace Modules\Order\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Order\App\Rules\ValidPaymentMethod;

class OrderFailureRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'orderId' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'orderId.required' => 'Order id is required.',
            'orderId.integer' => 'Order id must be an integer.',
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
