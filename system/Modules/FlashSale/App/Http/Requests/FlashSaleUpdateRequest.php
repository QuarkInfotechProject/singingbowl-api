<?php
namespace Modules\FlashSale\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\FlashSale\App\Models\FlashSale;

class FlashSaleUpdateRequest extends FormRequest
{
    /**
     * The flash sale being updated
     */
    protected ?FlashSale $flashSale = null;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:flash_sales,id',
            'campaign_name' => 'sometimes|required|string|min:2|max:255|unique:flash_sales,campaign_name,'.$this->id,
            'product_id' => 'sometimes|required|array|min:1',
            'product_id.*' => 'uuid|exists:products,uuid',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'theme_color' => 'sometimes|required|integer|exists:colors,id',
            'text_color' => 'sometimes|required|integer|exists:colors,id',
            'desktopBanner' => 'nullable|integer|exists:files,id',
            'mobileBanner' => 'nullable|integer|exists:files,id',
        ];
    }

    /**
     * Get the current flash sale being updated.
     */
    protected function getFlashSale(): FlashSale
    {
        if (!$this->flashSale) {
            $this->flashSale = FlashSale::findOrFail($this->id);
        }

        return $this->flashSale;
    }

    /**
     * Custom error messages for validation failures.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'The ID is required.',
            'id.integer' => 'The ID must be an integer.',
            'id.exists' => 'The provided ID does not exist in the flash_sales table.',
            'campaign_name.required' => 'The campaign name is required.',
            'campaign_name.string' => 'The campaign name must be a string.',
            'campaign_name.min' => 'The campaign name must be at least :min characters.',
            'campaign_name.max' => 'The campaign name may not be greater than :max characters.',
            'campaign_name.unique' => 'A campaign with this name already exists.',
            'start_date.required' => 'The start date is required.',
            'start_date.date' => 'The start date must be a valid date.',
            'end_date.required' => 'The end date is required.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after' => 'The end date must be after the start date.',
            'theme_color.required' => 'Please select a theme color.',
            'theme_color.exists' => 'The selected theme color does not exist.',
            'text_color.required' => 'Please select a text color.',
            'text_color.exists' => 'The selected text color does not exist.',
            'product_id.required' => 'At least one product must be selected.',
            'product_id.array' => 'The product selection must be an array.',
            'product_id.*.uuid' => 'Each product UUID must be a valid UUID string.',
            'product_id.*.exists' => 'One or more selected products do not exist.',
            'desktopBanner.integer' => 'The desktop banner ID must be an integer.',
            'desktopBanner.exists' => 'The selected desktop banner does not exist.',
            'mobileBanner.integer' => 'The mobile banner ID must be an integer.',
            'mobileBanner.exists' => 'The selected mobile banner does not exist.',
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