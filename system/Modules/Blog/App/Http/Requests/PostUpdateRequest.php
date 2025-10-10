<?php

namespace Modules\Blog\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'title' => 'required|string|min:2|max:255',
            'slug' => 'required|string|min:2|max:255|regex:/^\S*$/',
            'readTime' => 'nullable|integer|min:0',
            'description' => 'required|min:2',
            'files.desktopImage' => 'required|integer|exists:files,id',
            'files.mobileImage' => 'required|integer|exists:files,id',
            'meta.metaTitle' => 'nullable|string|min:2|max:255',
            'meta.keywords' => 'nullable|array',
            'meta.keywords.*' => 'string',
            'meta.metaDescription' => 'nullable|string|min:2',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Id field is required.',
            'id.integer' => 'The id field must be of type integer.',

            'title.required' => 'The title is required.',
            'title.string' => 'The title must be a string.',
            'title.min' => 'The title must be at least :min characters.',
            'title.max' => 'The title may not be greater than :max characters.',

            'slug.required' => 'The slug is required.',
            'slug.string' => 'The slug must be a string.',
            'slug.min' => 'The slug must be at least :min characters.',
            'slug.max' => 'The slug may not be greater than :max characters.',
            'slug.regex' => 'The slug should not contain any white spaces.',

            'readTime.integer' => 'The read time must be an integer.',
            'readTime.min' => 'The read time must be at least :min.',

            'description.required' => 'The description is required.',
            'description.min' => 'The description must be at least :min characters.',

            'files.desktopImage.required' => 'The desktop image is required.',
            'files.desktopImage.integer' => 'The desktop image must be an integer.',
            'files.desktopImage.exists' => 'The selected desktop image does not exist.',

            'files.mobileImage.required' => 'The mobile image is required.',
            'files.mobileImage.integer' => 'The mobile image must be an integer.',
            'files.mobileImage.exists' => 'The selected mobile image does not exist.',

            'meta.metaTitle.string' => 'The meta title must be a string.',
            'meta.metaTitle.min' => 'The meta title must be at least :min characters.',
            'meta.metaTitle.max' => 'The meta title may not be greater than :max characters.',

            'meta.keywords.array' => 'The keywords must be an array.',
            'meta.keywords.*.string' => 'Each keyword must be a string.',

            'meta.metaDescription.string' => 'The meta description must be a string.',
            'meta.metaDescription.min' => 'The meta description must be at least :min characters.',
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
