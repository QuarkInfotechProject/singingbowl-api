<?php

namespace Modules\Gallery\App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GalleryUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:galleries,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:galleries,slug,' . $this->get('id')],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['integer', 'exists:files,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

