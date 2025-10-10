<?php

namespace Modules\User\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserResetPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'OTP' => 'required|numeric|digits:6',
            'password' => [
                'required',
                'string',
                Password::min(8),
                'regex:/^(?=.*[\d\W]).+$/',
            ],
            'confirmPassword' => 'required|same:password'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'OTP.required' => 'The OTP is required.',
            'OTP.numeric' => 'The OTP must be a number.',
            'OTP.digits' => 'The OTP must be 6 digits.',
            'password.required' => 'Don\'t forget to set a new password for your account.',
            'password.string' => 'Your new password should be a valid text string.',
            'password.min' => 'Hold on! Your new password must be at least :min characters long.',
            'password.regex' => 'Your new password must contain at least one number or one symbol for better security.',
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

