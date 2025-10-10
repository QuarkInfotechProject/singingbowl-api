<?php

namespace Modules\Color\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ColorCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'hex_code' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'status' => 'nullable|boolean',
        ];
    }

    /**
     * Customize the error messages for validation.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Color name is required.',
            'hex_code.required' => 'Hex code is required.',
            'hex_code.regex' => 'Hex code must be in the format #RRGGBB.',
            'status.boolean' => 'Status must be a boolean value (true or false).',
        ];
    }
}
