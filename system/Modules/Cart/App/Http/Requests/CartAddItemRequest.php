<?php

namespace Modules\Cart\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductVariant;

class CartAddItemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'products' => 'required|array',
            'products.*.productId' => 'required|string',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.variantUuid' => 'nullable|string'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // No transformation needed as we're validating the actual field names used in the request
    }

    /**
     * Get validated data with transformed keys to match our internal naming.
     */
    public function validatedWithTransformedKeys()
    {
        $validated = $this->validated();

        // Transform the products array to use our internal field names and resolve IDs
        if (isset($validated['products']) && is_array($validated['products'])) {
            foreach ($validated['products'] as &$product) {
                if (isset($product['productId'])) {
                    // Look up the product ID from UUID
                    $productModel = Product::where('uuid', $product['productId'])->first();
                    if ($productModel) {
                        $product['id'] = $productModel->id;
                    } else {
                        // If product not found, keep the UUID and let service handle the error
                        $product['id'] = $product['productId'];
                    }
                    unset($product['productId']);
                }

                if (isset($product['variantUuid'])) {
                    // Look up the variant ID from UUID
                    $variantModel = ProductVariant::where('uuid', $product['variantUuid'])->first();
                    if ($variantModel) {
                        $product['variant_id'] = $variantModel->id;
                    } else {
                        // If variant not found, keep the UUID and let service handle the error
                        $product['variant_id'] = $product['variantUuid'];
                    }
                    unset($product['variantUuid']);
                }
            }
        }

        return $validated;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}