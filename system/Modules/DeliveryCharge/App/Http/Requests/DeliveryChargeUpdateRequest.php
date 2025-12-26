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
            'id'             => ['required', 'integer', 'exists:delivery_charges,id'],
            'description'    => ['required', 'string', 'min:2', 'max:50'],
            'deliveryCharge' => ['nullable', 'numeric', 'min:0', 'max:10000'],
        ];
    }

    public function messages()
    {
        return [
            'id.required'      => 'Id field is required.',
            'id.integer'       => 'Id must be an integer value.',
            'id.exists'        => 'The selected delivery charge does not exist.',
            'description.required' => 'The description field is required.',
            'description.string'   => 'The description must be a string.',
            'description.min'      => 'The description must be at least :min characters.',
            'description.max'      => 'The description may not be greater than :max characters.',
            'deliveryCharge.numeric' => 'The delivery charge must be a number.',
            'deliveryCharge.min'   => 'The delivery charge must be at least :min.',
            'deliveryCharge.max'   => 'The delivery charge may not be greater than :max.',
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
