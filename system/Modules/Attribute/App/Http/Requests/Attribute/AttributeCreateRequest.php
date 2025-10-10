<?php

namespace Modules\Attribute\App\Http\Requests\Attribute;

use Illuminate\Foundation\Http\FormRequest;

class AttributeCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'attributeSetId' => ['required', 'integer', 'exists:attribute_sets,id'],
            'name' => ['required', 'string', 'min:2', 'max:50'],
            'values' => ['nullable', 'array'],
            'values.*' => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'attributeSetId.required' => 'Attribute set id field is required.',
            'attributeSetId.integer' => 'Attribute set id field must be an integer.',
            'name.required' => 'Name field is required.',
            'name.string' => 'Name must be a string.',
            'name.min' => 'Name must be at least :min characters long.',
            'name.max' => 'Name may not be greater than :max characters.',
            'url.required' => 'URL field is required.',
            'url.string' => 'URL must be a string.',
            'url.min' => 'URL must be at least :min characters long.',
            'url.max' => 'URL may not be greater than :max characters.',
            'url.regex' => 'The url should not contain any white spaces.',
            'values.array' => 'Values must be an array.',
            'values.*.string' => 'Each value must be a string.',
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
