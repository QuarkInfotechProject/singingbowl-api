<?php

namespace Modules\User\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Modules\User\App\Models\VerificationCode;
use Modules\SystemConfiguration\App\Models\SystemConfig;

class UserRegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'fullName' => 'required|string|min:2|max:50',
            'phoneNumber' => 'required|string|regex:/^\+\d{1,4}\s?\d{7,14}$/|unique:users,phone_no',
            'password' => [
                'required',
                'string',
                Password::min(8),
                'regex:/^(?=.*[\d\W]).+$/',
            ],
            'confirmPassword' => 'required|same:password',
            'verificationCode' => [
                'required',
                'string',
                'size:6',
                function ($attribute, $value, $fail) {
                    $this->validateVerificationCode($attribute, $value, $fail);
                },
            ],
        ];
    }

    /**
     * Custom validation for verification code
     */
    protected function validateVerificationCode($attribute, $value, $fail)
    {
        $email = $this->input('email');

        // Use your existing database-based verification system
        $verification = VerificationCode::where('email', $email)->latest()->first();

        if (!$verification) {
            $fail('No verification code found for this email. Please request a new one.');
            return;
        }

        // Check if code has expired
        if ($verification->expires_at < now()) {
            $fail('The verification code has expired. Please request a new one.');
            return;
        }

        // Check attempt limits (using your existing system config)
        $codeAttempts = SystemConfig::firstWhere('name', 'code_attempts')->value('value') ?? 3;
        if ($verification->attempts >= $codeAttempts) {
            $fail('Too many verification code attempts. Please request a new one.');
            return;
        }

        // Check if code matches
        if ($verification->code != $value) {
            // Increment attempts (matches your existing UserRegisterService logic)
            $verification->increment('attempts');
            $fail('The verification code is incorrect. Please check and try again.');
            return;
        }
    }

    /**
     * Mark verification code as used after successful validation
     */
    public function markVerificationCodeAsUsed()
    {
        $email = $this->input('email');

        // Mark the verification code as used in your database system
        $verification = VerificationCode::where('email', $email)->latest()->first();
        if ($verification) {
            $verification->delete(); // This matches your existing UserRegisterService logic
        }
    }

    public function messages()
    {
        return [
            // Email validation messages
            'email.required' => 'Please enter an email.',
            'email.email' => 'The entered email address is not a valid email.',
            'email.unique' => 'This email has already been taken. Please use another email.',

            // Full name validation messages
            'fullName.required' => 'Please enter your full name.',
            'fullName.string' => 'Full name must be a valid text.',
            'fullName.min' => 'Full name must be at least 2 characters long.',
            'fullName.max' => 'Full name cannot exceed 50 characters.',

            // Phone number validation messages
            'phoneNumber.required' => 'Please enter your phone number.',
            'phoneNumber.string' => 'Phone number must be a valid text.',
            'phoneNumber.regex' => 'Phone number must be in international format (e.g., +977 9874563210).',
            'phoneNumber.unique' => 'This phone number has already been registered. Please use another phone number.',

            // Password validation messages
            'password.required' => 'Please enter a password.',
            'password.string' => 'Password must be a valid text.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Your password must contain at least one number or one symbol for better security.',

            // Confirm password validation messages
            'confirmPassword.required' => 'Please enter your confirmation password.',
            'confirmPassword.same' => 'Password confirmation does not match the password.',

            // Verification code validation messages
            'verificationCode.required' => 'Please enter the verification code.',
            'verificationCode.string' => 'Verification code must be a valid text.',
            'verificationCode.size' => 'The verification code must be exactly 6 characters long.',
        ];
    }
}
