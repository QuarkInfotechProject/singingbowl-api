<?php

namespace Modules\Content\App\Http\Requests\InThePress;

use Illuminate\Foundation\Http\FormRequest;

class InThePressCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'files.desktopImage' => 'required|integer|exists:files,id',
            'files.mobileImage' => 'required|integer|exists:files,id',
            'title' => 'required|string|min:2|max:255',
            'link' => 'required|url',
            'publishedDate' => 'required|date'
        ];
    }

    public function messages(): array
    {
        return [
            'files.desktopImage.required' => 'The featured image is required.',
            'files.desktopImage.integer' => 'The featured image must be an integer.',
            'files.desktopImage.exists' => 'The selected featured image does not exist in our records.',

            'files.mobileImage.required' => 'The featured image is required.',
            'files.mobileImage.integer' => 'The featured image must be an integer.',
            'files.mobileImage.exists' => 'The selected featured image does not exist in our records.',

            'title.required' => 'The title is required.',
            'title.string' => 'The title must be a string.',
            'title.min' => 'The title must be at least :min characters.',
            'title.max' => 'The title may not be greater than :max characters.',

            'link.required' => 'The link is required.',
            'link.url' => 'The link must be a valid URL.',

            'publishedDate.required' => 'The published date is required.',
            'publishedDate.date' => 'The published date must be a valid date.',
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
