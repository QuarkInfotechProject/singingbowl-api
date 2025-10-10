<?php

namespace Modules\Warranty\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarrantyClaimCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|min:2|max:255',
            'phone' => 'required|integer|regex:/^[0-9]{10,15}$/',
            'productName' => 'required|string|min:2|max:255',
            'quantity' => 'required|numeric|min:1',
            'purchasedFrom' => 'required|min:2|max:255',
            'images.*' => 'required|image|mimes:jpeg,jpg,png,gif|max:1024',
            'description' => 'required|min:2|max:255',
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
            'name.min' => 'Your name must be at least 2 characters long.',
            'name.max' => 'Your name must not exceed 255 characters.',

            'email.required' => 'Please provide your email address.',
            'email.email' => 'Please provide a valid email address.',
            'email.min' => 'Your email must be at least 2 characters long.',
            'email.max' => 'Your email must not exceed 255 characters.',

            'phone.required' => 'Please provide your phone number.',
            'phone.string' => 'Your phone number must be a string.',
            'phone.regex' => 'Your phone number must be between 10 and 15 digits long.',

            'productName.required' => 'Please provide the product name.',
            'productName.string' => 'The product name must be a string.',
            'productName.min' => 'The product name must be at least 2 characters long.',
            'productName.max' => 'The product name must not exceed 255 characters.',

            'quantity.required' => 'Please provide the quantity.',
            'quantity.numeric' => 'The quantity must be a number.',
            'quantity.min' => 'The quantity must be at least 1.',

            'purchasedFrom.required' => 'Please provide the name of the store where you purchased the product.',
            'purchasedFrom.string' => 'The store name must be a string.',
            'purchasedFrom.min' => 'The store name must be at least 2 characters long.',
            'purchasedFrom.max' => 'The store name must not exceed 255 characters.',

            'images.*.required' => 'Please upload at least one image.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Each image must be a jpeg, jpg, png, or gif file.',
            'images.*.max' => 'Each image must not exceed 1MB in size.',

            'description.required' => 'Please provide a description.',
            'description.min' => 'The description must be at least 2 characters long.',
            'description.max' => 'The description must not exceed 255 characters.',

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
