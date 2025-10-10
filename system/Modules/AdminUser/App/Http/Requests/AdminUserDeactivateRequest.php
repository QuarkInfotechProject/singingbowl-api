<?php

namespace Modules\AdminUser\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserDeactivateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return([
            'uuid' => ['required', 'exists:admin_users'],
            'remarks' => ['required', 'string'],
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function messages()
    {
        return [
            'remarks.required' => 'The user remark must be a string.',
        ];
    }
}
