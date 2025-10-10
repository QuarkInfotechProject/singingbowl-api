<?php

namespace Modules\Order\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Order\App\Models\Order;

class OrderChangeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'orderId' => 'required|integer',
            'status' => ['required', Rule::in(array_keys(Order::$orderStatusMapping))],
        ];
    }

    public function messages()
    {
        return [
            'orderId.required' => 'Order ID is required.',
            'orderId.integer' => 'Order ID must be a valid integer.',

            'status.required' => 'Order status is required.',
            'status.in' => 'The selected order status is invalid. Valid statuses are: '
                . implode(', ', array_values(Order::$orderStatusMapping)) . '.'
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
