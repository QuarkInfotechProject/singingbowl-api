<?php

namespace Modules\Warranty\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarrantyRegistrationCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|min:2|max:255',
            'phone' => 'required|integer|regex:/^[0-9]{10,15}$/',
            'productName' => 'required|string|min:2|max:255',
            'quantity' => 'required|numeric|min:1',
            'dateOfPurchase' => 'required|date|before_or_equal:today',
            'purchasedFrom' => 'required|string|min:2|max:255',
            'orderId' => 'required|string|min:2|max:50',
            'address' => 'required|string|min:2|max:50',
            'countryName' => 'required|string|min:2|max:50',
            'provinceName' => 'required|string|min:2|max:50',
            'cityName' => 'required|string|min:2|max:50',
            'zoneName' => 'required|string|min:2|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please provide your name.',
            'name.string' => 'The name should be a valid text.',
            'name.min' => 'The name should be at least 2 characters long.',
            'name.max' => 'The name should not exceed 255 characters.',

            'email.required' => 'Please provide your email address.',
            'email.email' => 'The email address must be in a valid format, like name@example.com.',
            'email.min' => 'The email address should be at least 2 characters long.',
            'email.max' => 'The email address should not exceed 255 characters.',

            'phone.required' => 'Please provide your phone number.',
            'phone.string' => 'The phone number should be a valid text.',
            'phone.regex' => 'The phone number should be between 10 to 15 digits.',

            'productName.required' => 'Please provide the name of the product.',
            'productName.string' => 'The product name should be a valid text.',
            'productName.min' => 'The product name should be at least 2 characters long.',
            'productName.max' => 'The product name should not exceed 255 characters.',

            'quantity.required' => 'Please specify the quantity.',
            'quantity.numeric' => 'The quantity should be a valid number.',
            'quantity.min' => 'The quantity should be at least 1.',

            'dateOfPurchase.required' => 'Please provide the date of purchase.',
            'dateOfPurchase.date' => 'The date of purchase should be a valid date.',
            'dateOfPurchase.before_or_equal' => 'The date of purchase cannot be in the future.',

            'purchasedFrom.required' => 'Please specify where you purchased the product.',
            'purchasedFrom.string' => 'The purchase location should be a valid text.',
            'purchasedFrom.min' => 'The purchase location should be at least 2 characters long.',
            'purchasedFrom.max' => 'The purchase location should not exceed 255 characters.',

            'orderId.string' => 'The order ID should be a valid text.',
            'orderId.min' => 'The order ID should be at least 2 characters long.',
            'orderId.max' => 'The order ID should not exceed 50 characters.',

            'address.required' => 'Please provide your address.',
            'address.string' => 'The address should be a valid text.',
            'address.min' => 'The address should be at least 2 characters long.',
            'address.max' => 'The address should not exceed 255 characters.',
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
