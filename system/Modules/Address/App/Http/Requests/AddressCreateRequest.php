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
            'mobile' => 'required|integer|digits:10|unique:addresses,mobile',
            'backupMobile' => 'nullable|integer|digits:10|different:mobile',
            'address' => 'required|string|min:5|max:255',
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
            'first_name.required' => 'Please provide your first name.',
            'first_name.string' => 'Your first name should only include letters.',
            'first_name.min' => 'Your first name must be at least 2 characters long.',
            'first_name.max' => 'Your first name cannot be longer than 255 characters.',
            'first_name.regex' => 'Your first name can only include letters, spaces, and hyphens.',

            'last_name.required' => 'Please provide your last name.',
            'last_name.string' => 'Your last name should only include letters.',
            'last_name.min' => 'Your last name must be at least 2 characters long.',
            'last_name.max' => 'Your last name cannot be longer than 255 characters.',
            'last_name.regex' => 'Your last name can only include letters, spaces, and hyphens.',

            'mobile.required' => 'Please provide your mobile number.',
            'mobile.digits' => 'Your mobile number must be exactly 10 digits long.',
            'mobile.unique' => 'This mobile number is already registered. Please use a different number.',

            'backup_mobile.digits' => 'Your backup mobile number must be exactly 10 digits long.',
            'backup_mobile.different' => 'Your backup mobile number should be different from your primary mobile number.',

            'address.required' => 'Please provide your address.',
            'address.string' => 'Your address should be a valid text.',
            'address.min' => 'Your address must be at least 10 characters long.',
            'address.max' => 'Your address cannot be longer than 255 characters.',

            'countryName.required' => 'Please provide a country name.',
            'countryName.string' => 'Your country name should be a valid text.',

            'provinceId.required' => 'Please select a province.',
            'provinceId.integer' => 'Please provide province id.',
            'provinceName.required' => 'Please provide a province name.',
            'provinceName.string' => 'Your province name should be a valid text.',

            'cityId.required' => 'Please select a city.',
            'cityId.integer' => 'Please provide city id.',
            'cityName.required' => 'Please provide a city name.',
            'cityName.string' => 'Your city name should be a valid text.',

            'zoneId.required' => 'Please select a zone.',
            'zoneId.integer' => 'Please provide zone id.',
            'zoneName.required' => 'Please provide a zone name.',
            'zoneName.string' => 'Your zone name should be a valid text.',
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
