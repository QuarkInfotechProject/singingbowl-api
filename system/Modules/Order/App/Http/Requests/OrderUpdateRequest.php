<?php

namespace Modules\Order\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Order\App\Rules\ValidPaymentMethod;

class OrderUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'addressId' => 'required',
            'note' => 'nullable|string|min:5|max:255',
            'couponCodes.*' => 'nullable|string',
            'paymentMethod' => ['required', 'string', new ValidPaymentMethod],
            'termsAndConditions' => 'accepted'
        ];
    }

    public function messages()
    {
        return [
            'addressId.required' => 'Please select an address.',
            'note.string' => 'The note must be a string.',
            'note.min' => 'The note must be at least :min characters.',
            'note.max' => 'The note may not be greater than :max characters.',
            'couponCodes.*.string' => 'Invalid coupon code format.',
            'paymentMethod.required' => 'Please select a valid payment method.',
            'termsAndConditions.accepted' => 'Please accept the terms and conditions.'
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
