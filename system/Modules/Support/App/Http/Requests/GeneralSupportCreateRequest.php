<?php

namespace Modules\Support\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralSupportCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email:rfc,dns',
            'phone' => 'required|string',
            'message' => 'required|string|max:1000',
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
            'phone.regex' => 'Please enter a valid phone number.',
            'phone.min' => 'Your phone number seems too short. Please check and try again.',

            'message.required' => 'Message is required. Please keep it under 1000 characters.',
            'message.max' => 'Your message is too long. Please keep it under 1000 characters.',
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
