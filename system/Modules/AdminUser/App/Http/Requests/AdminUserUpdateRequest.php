<?php

namespace Modules\AdminUser\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\Password;

class AdminUserUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()],
            'groupId' => ['nullable', 'integer', 'exists:roles,id'],
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
            'name.required' => 'Please enter a full name of the user.',
            'name.string' => 'The user name must be a string.',
            'name.max' => 'The user name cannot exceed 255 characters.',
        ];
    }
}
