<?php

namespace Modules\Attribute\App\Http\Requests\Attribute;

use Illuminate\Foundation\Http\FormRequest;

class AttributeUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'attributeSetId' => ['required', 'integer', 'exists:attribute_sets,id'],
            'name' => ['required', 'string', 'min:2', 'max:50'],
            'values' => ['nullable', 'array'],
            'values.*.id' => ['nullable', 'integer', 'exists:attribute_values,id'],
            'values.*.value' => ['nullable', 'string'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id']
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'The :attribute field is required.',
            'id.integer' => 'The :attribute field must be an integer.',
            'attributeSetId.required' => 'The :attribute field is required.',
            'attributeSetId.integer' => 'The :attribute field must be an integer.',
            'attributeSetId.exists' => 'The selected :attribute does not exist.',
            'name.required' => 'The :attribute field is required.',
            'name.string' => 'The :attribute field must be a string.',
            'name.min' => 'The :attribute must be at least :min characters.',
            'name.max' => 'The :attribute may not be greater than :max characters.',
            'url.required' => 'The :attribute field is required.',
            'url.string' => 'The :attribute field must be a string.',
            'url.min' => 'The :attribute must be at least :min characters.',
            'url.max' => 'The :attribute may not be greater than :max characters.',
            'url.regex' => 'The url should not contain any white spaces.',
            'values.array' => 'The :attribute must be an array.',
            'values.*.id.integer' => 'The :attribute field must be an integer.',
            'values.*.id.exists' => 'The selected :attribute does not exist.',
            'values.*.value.string' => 'The :attribute field must be a string.',
            'category_ids.array' => 'The :attribute must be an array.',
            'category_ids.*.integer' => 'The :attribute field must be an integer.',
            'category_ids.*.exists' => 'The selected :attribute does not exist.',

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
