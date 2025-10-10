<?php

namespace Modules\Review\App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class ReviewReplyCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'reviewId' => 'required|uuid',
            'content' => 'required|string|min:2|max:255'
        ];
    }

    public function messages()
    {
        return [
            'reviewId.required' => 'The review ID is required.',
            'reviewId.uuid' => 'The review ID must be a valid number.',
            'content.required' => 'Please provide content for your review.',
            'content.string' => 'The review content must be a valid string.',
            'content.min' => 'The review content must be at least :min characters long.',
            'content.max' => 'The review content may not be greater than :max characters long.',
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
