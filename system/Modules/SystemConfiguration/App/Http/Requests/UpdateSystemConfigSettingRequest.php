<?php

namespace Modules\SystemConfiguration\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemConfigSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'section' => 'required|exists:system_config_settings,section',
            'configs.*.id' => 'required|exists:system_config_settings,uuid',
            'configs.*.value' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'section' => 'Section is required.',
            'section.exists' => 'Section doesn\'t exist.',
            'configs.*.id.required' => 'Id is required for all configurations.',
            'configs.*.id.exists' => 'The system setting doesn\'t exist.',
            'configs.*.value.required' => 'Value is required for all configurations.',
            'configs.*.value.string' => 'Value must be a string.'
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
