<?php

namespace Modules\Address\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Address\App\Models\Address; // Import the Address model

class AddressUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // 1. Get the current logged-in user
        $user = $this->user();

        // 2. Find the existing address for this user to get its ID
        // We match the logic in your Service: finding the user's first address.
        $address = Address::where('user_id', $user->id)->first();
        $ignoreId = $address ? $address->id : null;

        return [
            'firstName' => 'required|string|min:2|max:255|regex:/^[\pL\s\-]+$/u',
            'lastName' => 'required|string|min:2|max:255|regex:/^[\pL\s\-]+$/u',
            'email' => 'required|email|max:255',

            // MOBILE VALIDATION FIX
            'mobile' => [
                'required',
                'integer',
                'digits:10',
                // This tells Laravel: "Check if unique, but ignore the record with this ID"
                Rule::unique('addresses', 'mobile')->ignore($ignoreId),
            ],
            
            'backupMobile' => 'nullable|integer|digits:10|different:mobile',

            'addressLine1' => 'required|string|min:5|max:255',
            'addressLine2' => 'nullable|string|max:255',

            'postalCode' => 'required|string|max:20',
            'landmark' => 'nullable|string|max:255',

            'addressType' => 'nullable|string|in:home,office,other',
            'deliveryInstructions' => 'nullable|string|max:500',
            'isDefault' => 'boolean',
            'label' => 'nullable|string|max:255',

            'countryCode' => 'required|string|max:10',
            'countryName' => 'required|string',
            'provinceId' => 'required|integer',
            'provinceName' => 'required|string',
            'cityId' => 'required|integer',
            'cityName' => 'required|string',
            'zoneId' => 'required|integer',
            'zoneName' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'firstName.required' => 'Please provide your first name.',
            'firstName.string' => 'Your first name should only include letters.',
            'firstName.min' => 'Your first name must be at least 2 characters long.',
            'firstName.regex' => 'Your first name can only include letters, spaces, and hyphens.',

            'lastName.required' => 'Please provide your last name.',
            'lastName.regex' => 'Your last name can only include letters, spaces, and hyphens.',

            'email.required' => 'Please provide your email address.',
            'email.email' => 'Please provide a valid email address.',

            'mobile.required' => 'Please provide your mobile number.',
            'mobile.digits' => 'Your mobile number must be exactly 10 digits long.',
            'mobile.unique' => 'This mobile number is already registered by another user.',

            'backupMobile.digits' => 'Your backup mobile number must be exactly 10 digits long.',
            'backupMobile.different' => 'Backup number must be different from primary mobile.',

            'addressLine1.required' => 'Please provide your main address (Line 1).',
            'addressLine1.min' => 'Address must be at least 5 characters long.',

            'postalCode.required' => 'Postal code is required.',
            'addressType.in' => 'Address type must be either home, office, or other.',

            'countryCode.required' => 'Country Code is required.',
            'countryName.required' => 'Country name is missing.',
            'provinceId.required' => 'Please select a province.',
            'cityId.required' => 'Please select a city.',
            'zoneId.required' => 'Please select a zone.',
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
