<?php

namespace Modules\Content\App\Http\Requests\FlashOffer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FlashOfferCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:50'],
            'files.desktopFile' => ['required', 'integer', 'exists:files,id'],
            'files.mobileFile' => ['required', 'integer', 'exists:files,id'],
            'link' => ['nullable', 'url'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'files.desktopFile.integer' => 'The desktop image must be an integer.',
            'files.desktopFile.exists' => 'The selected desktop image does not exist.',
            'files.mobileFile.integer' => 'The mobile image must be an integer.',
            'files.mobileFile.exists' => 'The selected mobile image does not exist.',
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
