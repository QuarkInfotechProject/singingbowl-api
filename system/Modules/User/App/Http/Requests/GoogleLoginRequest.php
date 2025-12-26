<?php

namespace Modules\User\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'google_id' => 'required|string',
            'avatar' => 'nullable|url',
            'id_token' => 'nullable|string',
            'access_token' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email is required for Google login.',
            'email.email' => 'Please provide a valid email address.',
            'name.required' => 'Name is required for Google login.',
            'google_id.required' => 'Google ID is required for authentication.',
        ];
    }
}
