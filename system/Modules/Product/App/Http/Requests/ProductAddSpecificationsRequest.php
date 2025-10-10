<?php
//
//namespace Modules\Product\App\Http\Requests;
//
//use Illuminate\Foundation\Http\FormRequest;
//use Modules\Product\App\Rules\UniqueOptionName;
//use Modules\Product\App\Rules\UniqueOptionValue;
//use Modules\Product\App\Rules\UniqueSkuRule;
//use Modules\Product\App\Rules\WithoutSpacesRule;
//
//class ProductAddSpecificationsRequest extends FormRequest
//{
//    /**
//     * Get the validation rules that apply to the request.
//     */
//    public function rules(): array
//    {
//        return [
//            'productId' => 'required|uuid',
//            'specifications.*.label' => 'required|string|min:2|max:50',
//            'specifications.*.icon' => 'nullable|image|mimes:jpeg,png,svg|max:1024'
//        ];
//    }
//
//    public function messages()
//    {
//        return [
//            'productId.required' => 'The product ID is required.',
//            'productId.uuid' => 'The product ID must be a valid UUID.',
//
//            'label.required' => 'The label is required.',
//            'label.string' => 'The label must be a string.',
//            'label.min' => 'The label must be at least 2 characters long.',
//            'label.max' => 'The label may not be longer than 50 characters.',
//
//            'icon.required' => 'An icon is required.',
//            'icon.mimes' => 'The icon must be a file of type: jpeg, png, svg.',
//            'icon.max' => 'The icon size may not be greater than 1MB.'
//        ];
//    }
//
//    /**
//     * Determine if the user is authorized to make this request.
//     */
//    public function authorize(): bool
//    {
//        return true;
//    }
//}
