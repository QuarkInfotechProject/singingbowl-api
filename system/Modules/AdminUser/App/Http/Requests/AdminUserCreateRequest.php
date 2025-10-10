<?php

namespace Modules\AdminUser\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class AdminUserCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admin_users'],
            'password' => ['required', Rules\Password::defaults()],
            'groupId' => ['required', 'integer', 'exists:roles,id']
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter a name for the admin user.',
            'name.string' => 'The user name must be a string.',
            'name.max' => 'The user name cannot exceed 255 characters.',
            'email.required' => 'Please enter a email address.',
            'email.string' => 'Email must be a string.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'password.required' => 'Please enter a password.',
        ];
    }
}
