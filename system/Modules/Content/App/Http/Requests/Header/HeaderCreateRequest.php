<?php

namespace Modules\Content\App\Http\Requests\Header;

use Illuminate\Foundation\Http\FormRequest;

class HeaderCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'text' => 'required|string|min:2|max:255',
            'link' => 'nullable|url',
        ];
    }

    public function messages(): array
    {
        return [
            'text.required' => 'The text is required.',
            'text.string' => 'The text must be a string.',
            'text.min' => 'The text must be at least :min characters.',
            'text.max' => 'The text may not be greater than :max characters.',

            'link.url' => 'The link must be a valid URL.',
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
