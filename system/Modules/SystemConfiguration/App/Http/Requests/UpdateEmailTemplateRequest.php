<?php

namespace Modules\SystemConfiguration\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailTemplateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:500'],
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
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
