<?php

namespace Modules\User\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => 'required|uuid',
            'email' => 'required|email',
            'fullName' => 'required|string|min:2|max:50',
            'phoneNo' => 'required|string|regex:/^9\d{9}$/',
            'dateOfBirth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'offersNotification' => 'required|boolean',

            'billingAddress' => 'nullable|array',

            'billingAddress.firstName' => 'nullable|required_with:billingAddress|string|min:2|max:255|regex:/^[\pL\s\-]+$/u',
            'billingAddress.lastName' => 'nullable|required_with:billingAddress|string|min:2|max:255|regex:/^[\pL\s\-]+$/u',
            'billingAddress.mobile' => 'nullable|required_with:billingAddress|string|digits:10',
            'billingAddress.backupMobile' => 'nullable|string|digits:10|different:billingAddress.mobile',
            'billingAddress.address' => 'nullable|required_with:billingAddress|string|min:5|max:255',
            'billingAddress.countryName' => 'nullable|required_with:billingAddress|string',
            'billingAddress.provinceId' => 'nullable|required_with:billingAddress|integer',
            'billingAddress.provinceName' => 'nullable|required_with:billingAddress|string',
            'billingAddress.cityId' => 'nullable|required_with:billingAddress|integer',
            'billingAddress.cityName' => 'nullable|required_with:billingAddress|string',
            'billingAddress.zoneId' => 'nullable|required_with:billingAddress|integer',
            'billingAddress.zoneName' => 'nullable|required_with:billingAddress|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'The user ID is required.',
            'id.uuid' => 'The user ID must be a valid UUID.',

            'email.required' => 'The email address is required.',
            'email.email' => 'The email address must be valid.',
            'email.unique' => 'This email address is already taken.',

            'fullName.required' => 'The full name is required.',
            'fullName.string' => 'The full name must be a string.',
            'fullName.min' => 'The full name must be at least :min characters.',
            'fullName.max' => 'The full name must not exceed :max characters.',

            'phoneNo.required' => 'The phone number is required.',
            'phoneNo.string' => 'The phone number must be a string.',
            'phoneNo.regex' => 'The phone number must start with 9 and be exactly 10 digits.',
            'phoneNo.unique' => 'This phone number is already taken.',

            'dateOfBirth.date' => 'The date of birth must be a valid date.',

            'gender.in' => 'The gender must be one of the following: male, female, or other.',

            'offersNotification.required' => 'The offers notification field is required.',
            'offersNotification.boolean' => 'The offers notification field must be true or false.',

            'billingAddress.firstName.required' => 'The first name is required.',
            'billingAddress.firstName.string' => 'The first name must be a string.',
            'billingAddress.firstName.min' => 'The first name must be at least :min characters.',
            'billingAddress.firstName.max' => 'The first name must not exceed :max characters.',
            'billingAddress.firstName.regex' => 'The first name can only contain letters, spaces, and hyphens.',

            'billingAddress.lastName.required' => 'The last name is required.',
            'billingAddress.lastName.string' => 'The last name must be a string.',
            'billingAddress.lastName.min' => 'The last name must be at least :min characters.',
            'billingAddress.lastName.max' => 'The last name must not exceed :max characters.',
            'billingAddress.lastName.regex' => 'The last name can only contain letters, spaces, and hyphens.',

            'billingAddress.mobile.required' => 'The mobile number is required.',
            'billingAddress.mobile.string' => 'The mobile number must be a string.',
            'billingAddress.mobile.digits' => 'The mobile number must be exactly 10 digits.',

            'billingAddress.backupMobile.digits' => 'The backup mobile number must be exactly 10 digits.',
            'billingAddress.backupMobile.different' => 'The backup mobile number must be different from the primary mobile number.',

            'billingAddress.address.required' => 'The address is required.',
            'billingAddress.address.min' => 'The address must be at least :min characters.',
            'billingAddress.address.max' => 'The address must not exceed :max characters.',

            'billingAddress.countryName.required' => 'The country name is required.',
            'billingAddress.provinceId.required' => 'The province ID is required.',
            'billingAddress.provinceId.integer' => 'The province ID must be an integer.',
            'billingAddress.provinceName.required' => 'The province name is required.',
            'billingAddress.cityId.required' => 'The city ID is required.',
            'billingAddress.cityId.integer' => 'The city ID must be an integer.',
            'billingAddress.cityName.required' => 'The city name is required.',
            'billingAddress.zoneId.required' => 'The zone ID is required.',
            'billingAddress.zoneId.integer' => 'The zone ID must be an integer.',
            'billingAddress.zoneName.required' => 'The zone name is required.'
        ];
    }
}
