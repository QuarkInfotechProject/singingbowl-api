<?php

namespace Modules\Support\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderSupportCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email:rfc,dns',
            'phone' => ['required', 'string', 'regex:/^\+?\d{1,4}[\s-]?\d{7,14}$/'],
            'orderId' => 'required|integer',
            'paymentTransactionId' => 'nullable|string|max:100',
            'message' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'name.string' => 'Your name should only contain letters and spaces.',
            'name.min' => 'Your name seems too short. Please enter at least 2 characters.',
            'name.max' => 'Your name is too long. Please keep it under 255 characters.',

            'email.required' => 'We need your email address to contact you.',
            'email.email' => 'This doesn\'t look like a valid email address. Please check and try again.',

            'phone.required' => 'Please enter your phone number.',
            'phone.regex' => 'Please enter a valid phone number (e.g., +977 9851234567 or 9851234567).',
            'phone.min' => 'Your phone number seems too short. Please check and try again.',

            'orderId.required' => 'The order ID is required.',
            'orderId.integer' => 'The order ID must be an integer value.',

            'paymentTransactionId.max' => 'The payment transaction ID is too long. Please enter no more than 100 characters.',

            'message.required' => 'Message is required. Please keep it under 1000 characters.',
            'message.max' => 'Your message is too long. Please keep it under 1000 characters.',

            'image.image' => 'The file you uploaded is not an image. Please upload only images.',
            'image.mimes' => 'Please upload images in JPEG, PNG, JPG, or GIF format.',
            'image.max' => 'The image is too large. Please upload an image smaller than 1MB.',
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
