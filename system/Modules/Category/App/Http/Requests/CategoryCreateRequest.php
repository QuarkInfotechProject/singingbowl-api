<?php

namespace Modules\Category\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'url' => ['required', 'string', 'min:2', 'max:50', 'unique:categories,slug', 'regex:/^\S*$/'],
            'searchable' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
            'files.logo' => ['nullable', 'integer', 'exists:files,id'],
            'files.banner' => ['nullable', 'integer', 'exists:files,id'],
            'parentId' => ['required', 'integer'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter a name for the category.',
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
            'url.required' => 'Please enter a URL for the category.',
            'url.string' => 'The category URL must be a string.',
            'url.min' => 'The category URL must be at least 2 characters long.',
            'url.max' => 'The category URL cannot exceed 50 characters.',
            'url.regex' => 'The url should not contain any white spaces.',
            'parentId.integer' => 'The parent ID must be an integer.',
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
