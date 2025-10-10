<?php

namespace Modules\OrderProcessing\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefundProcessRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'refundAmount' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:255',
            'restockItems' => 'required|boolean',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'refundAmount.required' => 'The refund amount is required.',
            'refundAmount.numeric' => 'The refund amount must be a valid number.',
            'refundAmount.min' => 'The refund amount must be at least 0.',

            'reason.string' => 'The reason must be a valid string.',
            'reason.max' => 'The reason must not exceed 255 characters.',

            'restockItems.required' => 'You must specify whether to restock items.',
            'restockItems.boolean' => 'The restock items field must be true or false.',

            'items.required' => 'You must select at least one item.',
            'items.array' => 'The items field must be an array.',

            'items.*.id.required' => 'Each item must have an ID.',
            'items.*.id.exists' => 'The selected item ID is invalid.',

            'items.*.quantity.required' => 'The quantity for each item is required.',
            'items.*.quantity.integer' => 'The quantity must be an integer.',
            'items.*.quantity.min' => 'The quantity must be at least 0.',
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
