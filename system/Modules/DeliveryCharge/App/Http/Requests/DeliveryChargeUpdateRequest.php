<?php

namespace Modules\DeliveryCharge\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryChargeUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id'                    => ['required', 'integer', 'exists:delivery_charges,id'], // Added exists check for safety
            'description'           => ['required', 'string', 'min:2', 'max:50'],
            'deliveryCharge'        => ['nullable', 'numeric', 'min:0', 'max:1000'], // Changed to nullable to match CreateRequest
            'additionalChargePerItem' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'weightBasedCharge'     => ['nullable', 'numeric', 'min:0', 'max:1000'],

            // New Fields
            'country'               => ['nullable', 'string', 'max:100'],
            'countryCode'           => ['nullable', 'string', 'max:10'],
            'chargeAbove20kg'       => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'chargeAbove45kg'       => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'chargeAbove100kg'      => ['nullable', 'numeric', 'min:0', 'max:10000'],
        ];
    }

    public function messages()
    {
        return [
            // ID Messages
            'id.required'                   => 'Id field is required.',
            'id.integer'                    => 'Id must be an integer value.',
            'id.exists'                     => 'The selected delivery charge does not exist.',

            // Existing Messages
            'description.required'          => 'The description field is required.',
            'description.string'            => 'The description must be a string.',
            'description.min'               => 'The description must be at least :min characters.',
            'description.max'               => 'The description may not be greater than :max characters.',
            
            'deliveryCharge.numeric'        => 'The delivery charge must be a number.',
            'deliveryCharge.min'            => 'The delivery charge must be at least :min.',
            'deliveryCharge.max'            => 'The delivery charge may not be greater than :max.',
            
            'additionalChargePerItem.numeric' => 'The additional charge per item must be a number.',
            'additionalChargePerItem.min'   => 'The additional charge per item must be at least :min.',
            'additionalChargePerItem.max'   => 'The additional charge per item may not be greater than :max.',
            
            'weightBasedCharge.numeric'     => 'The weight-based charge must be a number.',
            'weightBasedCharge.min'         => 'The weight-based charge must be at least :min.',
            'weightBasedCharge.max'         => 'The weight-based charge may not be greater than :max.',

            // New Messages
            'country.string'                => 'The country must be a string.',
            'country.max'                   => 'The country name may not be greater than :max characters.',
            
            'countryCode.string'            => 'The country code must be a string.',
            'countryCode.max'               => 'The country code may not be greater than :max characters.',

            'chargeAbove20kg.numeric'       => 'The charge for above 20kg must be a number.',
            'chargeAbove20kg.min'           => 'The charge for above 20kg must be at least :min.',
            'chargeAbove20kg.max'           => 'The charge for above 20kg may not be greater than :max.',

            'chargeAbove45kg.numeric'       => 'The charge for above 45kg must be a number.',
            'chargeAbove45kg.min'           => 'The charge for above 45kg must be at least :min.',
            'chargeAbove45kg.max'           => 'The charge for above 45kg may not be greater than :max.',

            'chargeAbove100kg.numeric'      => 'The charge for above 100kg must be a number.',
            'chargeAbove100kg.min'          => 'The charge for above 100kg must be at least :min.',
            'chargeAbove100kg.max'          => 'The charge for above 100kg may not be greater than :max.',
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
