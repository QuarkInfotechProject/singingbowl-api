<?php

namespace Modules\Address\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'firstName' => 'required|string|min:2|max:255|regex:/^[\pL\s\-]+$/u',
            'lastName' => 'required|string|min:2|max:255|regex:/^[\pL\s\-]+$/u',
            'email' => 'required|email|max:255', // New field
            
            'mobile' => 'required|integer|digits:10|unique:addresses,mobile',
            'backupMobile' => 'nullable|integer|digits:10|different:mobile',
            
            // Renamed from 'address' to 'addressLine1'
            'addressLine1' => 'required|string|min:5|max:255',
            'addressLine2' => 'nullable|string|max:255', // New field
            
            'postalCode' => 'required|string|max:20', // New field
            'landmark' => 'nullable|string|max:255', // New field
            
            'addressType' => 'nullable|string|in:home,office,other', // New field with validation
            'deliveryInstructions' => 'nullable|string|max:500', // New field
            'isDefault' => 'boolean', // New field
            'label' => 'nullable|string|max:255', // New field

            'countryId' => 'required|integer', // Added ID validation if needed, assuming you send both
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
            // Name
            'firstName.required' => 'Please provide your first name.',
            'firstName.string' => 'Your first name should only include letters.',
            'firstName.min' => 'Your first name must be at least 2 characters long.',
            'firstName.regex' => 'Your first name can only include letters, spaces, and hyphens.',

            'lastName.required' => 'Please provide your last name.',
            'lastName.regex' => 'Your last name can only include letters, spaces, and hyphens.',

            // Email
            'email.required' => 'Please provide your email address.',
            'email.email' => 'Please provide a valid email address.',

            // Mobile
            'mobile.required' => 'Please provide your mobile number.',
            'mobile.digits' => 'Your mobile number must be exactly 10 digits long.',
            'mobile.unique' => 'This mobile number is already registered.',
            'backupMobile.different' => 'Backup number must be different from primary mobile.',

            // Address Lines
            'addressLine1.required' => 'Please provide your main address (Line 1).',
            'addressLine1.min' => 'Address must be at least 5 characters long.',
            
            // Postal Code
            'postalCode.required' => 'Postal code is required.',

            // Address Type
            'addressType.in' => 'Address type must be either home, office, or other.',

            // Location details
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
