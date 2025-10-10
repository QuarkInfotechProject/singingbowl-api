<?php

namespace Modules\Content\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContentCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'files.desktopImage' => ['required', 'integer', 'exists:files,id'],
            'files.mobileImage' => ['required', 'integer', 'exists:files,id'],
            'link' => ['nullable', 'url'],
            'type' => ['required', 'integer', Rule::in(['1', '2', '3', '5', '6'])]
        ];
    }

    public function messages(): array
    {
        return [
            'files.desktopImage.integer' => 'The desktop image must be an integer.',
            'files.desktopImage.exists' => 'The selected desktop image does not exist.',
            'files.mobileImage.integer' => 'The mobile image must be an integer.',
            'files.mobileImage.exists' => 'The selected mobile image does not exist.',
            'link.url' => 'The link format is invalid.',
            'type.required' => 'The type field is required.',
            'type.integer' => 'The type field must be an integer value specifying the content type.'
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
