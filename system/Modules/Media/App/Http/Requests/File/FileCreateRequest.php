<?php

namespace Modules\Media\App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

class FileCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'files.*' => ['required', 'mimes:jpeg,jpg,png,gif,mp4,webp', 'max:5000'],
            'fileCategoryId' => ['nullable', 'integer', 'exists:file_categories,id'],
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
