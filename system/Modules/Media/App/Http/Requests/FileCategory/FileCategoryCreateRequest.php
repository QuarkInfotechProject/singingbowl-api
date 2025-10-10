<?php

namespace Modules\Media\App\Http\Requests\FileCategory;

use Illuminate\Foundation\Http\FormRequest;

class FileCategoryCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'url' => ['required', 'string', 'max:255', 'min:2', 'unique:file_categories,slug', 'regex:/^\S*$/']
        ];
    }

    public function messages()
    {
        return [
            'url.regex' => 'The url should not contain any white spaces.',
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
