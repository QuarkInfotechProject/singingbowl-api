<?php

namespace Modules\Attribute\App\Http\Requests\CategoryAttribute;

use Illuminate\Foundation\Http\FormRequest;

class CategoryAttributesByCategoriesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'integer|exists:categories,id',
            'include_values' => 'sometimes|boolean',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
