<?php

namespace Modules\Review\App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class ReviewCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'productId' => ['required', 'exists:products,uuid'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:600'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    public function messages()
    {
        return [
            'productId.required' => 'Please specify which item you are rating.',
            'productId.exists' => 'The selected item is not found or is invalid. Please check and try again.',
            'rating.required' => 'Please provide a rating for your experience.',
            'rating.integer' => 'Your rating should be a number between 1 and 5.',
            'rating.between' => 'Your rating should be between 1 and 5.',
            'comment.string' => 'Your comment should be a text.',
            'comment.max' => 'Your comment should be no longer than 600 characters.',
            'images.*.image' => 'Each file in the images should be a valid image file.',
            'images.*.mimes' => 'Images should be in JPEG, PNG, JPG, or GIF format.',
            'images.*.max' => 'Each image should not exceed 2048 kilobytes in size.',
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
