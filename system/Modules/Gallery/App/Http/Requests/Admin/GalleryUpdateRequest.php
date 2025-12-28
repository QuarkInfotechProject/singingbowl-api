<?php

namespace Modules\Gallery\App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GalleryUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:galleries,id'],
            'images' => ['nullable', 'array'],
            'images.*' => ['integer', 'exists:files,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

