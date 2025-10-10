<?php

namespace Modules\Attribute\App\Http\Requests\Attribute;

use Illuminate\Foundation\Http\FormRequest;

class AttributeIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
