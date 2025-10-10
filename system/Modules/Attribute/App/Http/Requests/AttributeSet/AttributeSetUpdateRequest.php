<?php

namespace Modules\Attribute\App\Http\Requests\AttributeSet;

use Illuminate\Foundation\Http\FormRequest;

class AttributeSetUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'name' => ['required', 'string', 'min:2', 'max:50']
        ];
    }

    public function messages()
    {
        return [
            'url.regex' => 'The url should not contain any white spaces.',
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
