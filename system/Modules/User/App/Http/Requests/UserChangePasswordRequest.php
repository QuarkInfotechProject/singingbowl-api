<?php

namespace Modules\User\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserChangePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'currentPassword' => 'required|current_password',
            'newPassword' => [
                'required',
                'string',
                Password::min(8),
                'regex:/^(?=.*[\d\W]).+$/',
                'different:currentPassword'
            ],
            'confirmPassword' => 'required|same:newPassword'
        ];
    }

    public function messages()
    {
        return [
            'currentPassword.required' => 'Please enter your current password to confirm your identity.',
            'currentPassword.current_password' => 'Oops! The current password you entered is not correct. Double-check and try again.',
            'newPassword.required' => 'Don\'t forget to set a new password for your account.',
            'newPassword.string' => 'Your new password should be a valid text string.',
            'newPassword.min' => 'Hold on! Your new password must be at least :min characters long.',
            'newPassword.regex' => 'Your new password must contain at least one number or one symbol for better security.',
            'newPassword.different' => 'The new password must be different from your current password. Please choose a unique and secure password.',
            'confirmPassword.required' => 'Confirm your new password to make sure it\'s just right.',
            'confirmPassword.same' => 'Hold on! The confirm password doesn\'t match your new password. Please check and try again.',
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
