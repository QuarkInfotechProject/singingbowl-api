<?php

namespace Modules\User\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'phoneNumber' => ['nullable', 'string', 'regex:/^\+?[0-9\s]{7,20}$/'],
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'offersNotification' => 'boolean',
            'gender' => 'nullable|in:male,female,other',
            'dateOfBirth' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'phoneNumber.regex' => 'Please enter a valid phone number with country code (e.g., +1234567890 or +977-9812345678).',
            'profilePicture.image' => 'Please upload a valid image for your profile picture.',
            'profilePicture.mimes' => 'Your profile picture must be in one of the following formats: jpeg, png, jpg, gif.',
            'profilePicture.max' => 'Your profile picture should not exceed 5MB in size.',
            'offersNotification.boolean' => 'Please select a valid option for offers notification.',
            'gender.in' => 'Please select a valid gender: male, female, or other.',
            'dateOfBirth.date' => 'Please enter your date of birth in a valid date format (YYYY-MM-DD).',
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
