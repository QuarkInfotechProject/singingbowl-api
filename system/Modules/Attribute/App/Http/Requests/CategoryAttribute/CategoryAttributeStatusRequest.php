<?php

namespace Modules\Attribute\App\Http\Requests\CategoryAttribute;

use Illuminate\Foundation\Http\FormRequest;

class CategoryAttributeStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category_id'   => 'required|integer|exists:categories,id',
            'attribute_id'  => 'required|integer|exists:attributes,id',
            'is_active'     => 'required|boolean',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
