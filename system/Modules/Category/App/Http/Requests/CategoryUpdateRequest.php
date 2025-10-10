<?php

namespace Modules\Category\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'searchable' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
            'files.logo' => ['nullable', 'integer', 'exists:files,id'],
            'files.banner' => ['nullable', 'integer', 'exists:files,id'],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Category id is required.',
            'id.integer' => 'Id must be an integer.',
            'name.required' => 'The category name is required.',
            'name.string' => 'The category name must be a string.',
            'name.max' => 'The category name cannot exceed 255 characters.',
            'description.string' => 'The description must be a string.',
            'searchable.required' => 'The searchable field is required.',
            'searchable.boolean' => 'The searchable field must be a boolean value (true or false).',
            'status.required' => 'The status field is required.',
            'status.boolean' => 'The status field must be a boolean value (true or false).',
            'files.logo.integer' => 'The logo must be an integer.',
            'files.logo.exists' => 'The selected logo file does not exist in the database.',
            'files.banner.integer' => 'The banner must be an integer.',
            'files.banner.exists' => 'The selected banner file does not exist in the database.',
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
