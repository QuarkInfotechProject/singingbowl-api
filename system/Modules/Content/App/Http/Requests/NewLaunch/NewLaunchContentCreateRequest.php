<?php

namespace Modules\Content\App\Http\Requests\NewLaunch;

use Illuminate\Foundation\Http\FormRequest;

class NewLaunchContentCreateRequest extends FormRequest
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
            'isBanner' => ['required', 'boolean']
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
            'isBanner.required' => 'The isBanner field is required.',
            'isBanner.boolean' => 'The isBanner field must be boolean value.'
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
