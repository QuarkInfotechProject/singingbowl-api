<?php

namespace Modules\Review\App\Http\Requests\Question;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class QuestionCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'productId' => ['required', 'exists:products,uuid'],
            'comment' => ['required', 'string', 'min:2', 'max:255'],
        ];

        if (!Auth::check()) {
            $rules['name'] = ['required', 'string', 'min:2', 'max:30'];
            $rules['email'] = ['required', 'string', 'email'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'productId.required' => 'The product ID is required.',
            'productId.exists' => 'The selected product does not exist.',
            'name.required' => 'Your name is required if you are not logged in.',
            'name.string' => 'Your name must be a valid string.',
            'name.min' => 'Your name must be at least 5 characters long.',
            'name.max' => 'Your name may not be greater than 30 characters.',
            'email.required' => 'Your email is required if you are not logged in.',
            'email.string' => 'Your email must be a valid string.',
            'email.email' => 'Please enter a valid email address.',
            'comment.required' => 'Please enter a comment.',
            'comment.string' => 'The comment must be a valid string.',
            'comment.min' => 'The comment must be at least 2 characters long.',
            'comment.max' => 'The comment may not be greater than 255 characters.',
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
