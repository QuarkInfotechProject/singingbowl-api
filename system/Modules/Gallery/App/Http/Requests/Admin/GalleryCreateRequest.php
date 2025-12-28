<?php

namespace Modules\Gallery\App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GalleryCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'images' => ['nullable', 'array'],
            'images.*' => ['integer', 'exists:files,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

