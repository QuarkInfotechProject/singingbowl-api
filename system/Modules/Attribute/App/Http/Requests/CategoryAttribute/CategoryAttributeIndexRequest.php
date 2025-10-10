<?php

namespace Modules\Attribute\App\Http\Requests\CategoryAttribute;

use Illuminate\Foundation\Http\FormRequest;

class CategoryAttributeIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category_id' => 'required|integer|exists:categories,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
