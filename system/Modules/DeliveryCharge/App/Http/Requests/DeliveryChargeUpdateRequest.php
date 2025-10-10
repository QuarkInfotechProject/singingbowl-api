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
            'id' => ['required', 'integer'],
            'description' => ['required', 'string', 'min:2', 'max:50',],
            'deliveryCharge' => ['required', 'numeric', 'min:0', 'max:1000'],
            'additionalChargePerItem' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'weightBasedCharge' => ['nullable', 'numeric', 'min:0', 'max:1000']
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Id field is required.',
            'id.integer' => 'Id must be an integer value.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description must be a string.',
            'description.min' => 'The description must be at least :min characters.',
            'description.max' => 'The description may not be greater than :max characters.',
            'deliveryCharge.required' => 'The delivery charge field is required.',
            'deliveryCharge.numeric' => 'The delivery charge must be a number.',
            'deliveryCharge.min' => 'The delivery charge must be at least :min.',
            'deliveryCharge.max' => 'The delivery charge may not be greater than :max.',
            'additionalChargePerItem.numeric' => 'The additional charge per item must be a number.',
            'additionalChargePerItem.min' => 'The additional charge per item must be at least :min.',
            'additionalChargePerItem.max' => 'The additional charge per item may not be greater than :max.',
            'weightBasedCharge.numeric' => 'The weight-based charge must be a number.',
            'weightBasedCharge.min' => 'The weight-based charge must be at least :min.',
            'weightBasedCharge.max' => 'The weight-based charge may not be greater than :max.',
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
