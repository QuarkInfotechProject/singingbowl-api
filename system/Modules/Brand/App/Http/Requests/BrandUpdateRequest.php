<?php

namespace Modules\Brand\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:brands,id',
            'name' => 'required|string|min:2|max:255|unique:brands,name,'.$this->id,
            'slug' => 'required|string|min:2|max:255|unique:brands,slug,'.$this->id.'|regex:/^[a-zA-Z0-9-]+$/',
            'status' => 'required|boolean',
            'logo' => 'nullable|integer|exists:files,id',
            'banner' => 'nullable|integer|exists:files,id',
            'meta_title' => 'nullable|string|min:2|max:255',
            'meta_description' => 'nullable|string|min:2',
        ];
    }

    /**
     * Get the custom validation messages.
     */
    public function messages(): array
    {
        return [
           'id.required' => 'The brand ID is required.',
            'id.integer' => 'The brand ID must be an integer.',
            'id.exists' => 'The brand with this ID does not exist.',

            'name.required' => 'The brand name is required.',
            'name.string' => 'The brand name must be a string.',
            'name.unique' => 'The brand name has already been taken.',
            'name.min' => 'The brand name must be at least :min characters.',
            'name.max' => 'The brand name may not be greater than :max characters.',

            'slug.required' => 'The slug is required.',
            'slug.string' => 'The slug must be a string.',
            'slug.regex' => 'The slug format is invalid.',
            'slug.unique' => 'The slug has already been taken.',
            'slug.min' => 'The slug must be at least :min characters.',
            'slug.max' => 'The slug may not be greater than :max characters.',

            'status.required' => 'The status is required.',
            'status.boolean' => 'The status must be a boolean value.',

            'logo.integer' => 'The logo must be a valid file ID.',
            'logo.exists' => 'The selected logo does not exist.',

            'banner.integer' => 'The banner must be a valid file ID.',
            'banner.exists' => 'The selected banner does not exist.',

            'meta_title.string' => 'The meta title must be a string.',
            'meta_title.min' => 'The meta title must be at least :min characters.',
            'meta_title.max' => 'The meta title may not be greater than :max characters.',

            'meta_description.string' => 'The meta description must be a string.',
            'meta_description.min' => 'The meta description must be at least :min characters.',
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
