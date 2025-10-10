<?php

namespace Modules\Attribute\App\Http\Requests\CategoryAttribute;

use Illuminate\Foundation\Http\FormRequest;

class CategoryAttributeSortOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category_id'     => 'required|integer|exists:categories,id',
            'attribute_order' => 'required|array',
            'attribute_order.*' => 'integer|exists:attributes,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
