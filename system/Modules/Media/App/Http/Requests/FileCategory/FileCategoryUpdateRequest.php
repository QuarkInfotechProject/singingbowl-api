<?php

namespace Modules\Media\App\Http\Requests\FileCategory;

use Illuminate\Foundation\Http\FormRequest;

class FileCategoryUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'url' => ['required', 'string', 'min:2', 'max:255', 'regex:/^\S*$/'],
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
