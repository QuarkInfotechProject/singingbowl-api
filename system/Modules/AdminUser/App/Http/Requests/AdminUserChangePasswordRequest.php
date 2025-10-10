<?php

namespace Modules\AdminUser\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AdminUserChangePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'currentPassword' => 'required|current_password',
            'newPassword' => [
                'string',
                'required',
                'different:currentPassword',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'confirmPassword' => 'required|same:newPassword'
        ];
    }
}
