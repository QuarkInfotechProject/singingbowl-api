<?php

namespace Modules\Product\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Product\App\Rules\UniqueOptionName;
use Modules\Product\App\Rules\UniqueOptionValue;
use Modules\Product\App\Rules\UniqueSkuRule;
use Modules\Product\App\Rules\WithoutSpacesRule;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'uuid' => 'required|string',
            'productName' => 'required|string',
            'url' => 'required|string|regex:/^\S*$/',
            'brandId'=>'nullable|integer|exists:brands,id',
            'sortOrder' => 'required|integer|min:0',
            'bestSeller' => 'nullable|boolean',
            'hasVariant' => 'required|boolean',

            'originalPrice' => $this->getOriginalPriceRules(),
            'specialPrice' => 'nullable|numeric|min:0|lte:originalPrice',
            'specialPriceStart' => 'nullable|date|after_or_equal:today|required_with:specialPrice',
            'specialPriceEnd' => 'nullable|date|after:specialPriceStart|required_with:specialPriceStart',

            'sku' => $this->getSkuRules(),
            'description' => 'required|string',
            'additionalDescription' => 'nullable|string',
            'status' => 'required|boolean',

            'saleStart' => 'nullable|date|after_or_equal:today',
            'saleEnd' => 'nullable|date|after:saleStart',

            'quantity' => $this->getQuantityRules(),
            'inStock' => $this->getInStockRules(),

            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',

            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',

            'files.baseImage' => $this->getBaseImageRules(),
            'files.additionalImage.*' => 'nullable|exists:files,id',
            'files.descriptionVideo' => 'nullable|exists:files,id',

            'options' => $this->getOptionRules(),
            'options.*.uuid' => 'nullable|string|exists:product_options',
            'options.*.name' => ['required', 'string', new UniqueOptionName],

            'options.*.values' => 'required|array',
            'options.*.values.*.uuid' => 'nullable|string',
            'options.*.hasImage' => 'required|boolean',
            'options.*.values.*.optionName' => ['required', 'string', new UniqueOptionValue],
            'options.*.values.*.files' => 'nullable|array',
            'options.*.values.*.files.baseImage' => 'nullable|integer|exists:files,id',
            'options.*.values.*.files.additionalImage' => 'nullable|array|exists:files,id',

            'variants' => $this->getVariationRules(),
            'variants.*.name' => 'required|string',
            'variants.*.sku' => ['required', 'string', new WithoutSpacesRule],
            'variants.*.status' => 'required|boolean',
            'variants.*.originalPrice' => 'required|numeric|min:0',
            'variants.*.specialPrice' => 'nullable|numeric|min:0|lte:variants.*.originalPrice',
            'variants.*.quantity' => 'required|integer|min:0',
            'variants.*.inStock' => 'required|boolean',

            'meta.metaTitle' => 'nullable|string',
            'meta.keywords' => 'nullable|array',
            'meta.keywords.*' => 'string',
            'meta.metaDescription' => 'nullable|string',

            'attributes.*.attributeId' => 'nullable|exists:attributes,id',
            'attributes.*.values.*' => 'exists:attribute_values,id',

            'newFrom' => 'nullable|date|after_or_equal:today',
            'newTo' => 'nullable|date|after:newFrom|required_with:newFrom',

            'relatedProducts.*' => 'nullable|exists:products,uuid',
            'upSells.*' => 'nullable|exists:products,uuid',
            'crossSells.*' => 'nullable|exists:products,uuid',

            'couponId.*' => 'nullable|integer',
            'featureId.*' => 'nullable|integer',
            'specifications' => 'nullable|array|max:6',
            'specifications.*.icon' => 'nullable|string|min:3|max:255',
            'specifications.*.content' => 'nullable|string|min:3|max:255',

            'keySpecs' => 'nullable|array',
            'keySpecs.*.key' => 'required|string',
            'keySpecs.*.value' => 'required|array',
            'keySpecs.*.value.*' => 'string',
        ];
    }

    private function getOriginalPriceRules() {
        return request()->hasVariant ? 'nullable|numeric|min:0' : 'required|numeric|min:0';
    }

    private function getSkuRules() {
        return request()->hasVariant ? 'nullable|string|min:2|max:50' : 'required|string|min:2|max:50|unique:products,sku|regex:/^\S*$/';
    }

    private function getQuantityRules() {
        return request()->hasVariant ? 'nullable|integer|min:0' : 'required|integer|min:0';
    }

    private function getInStockRules()
    {
        return request()->hasVariant ? 'boolean' : 'required|boolean';
    }

    private function getBaseImageRules() {
        return request()->hasVariant ? 'nullable|integer|exists:files,id' : 'required|integer|exists:files,id';
    }

    private function getOptionRules() {
        return request()->hasVariant ? 'required|array' : 'nullable|array';
    }

    private function getVariationRules() {
        return request()->hasVariant ? 'required|array' : 'nullable|array';
    }

    private function isRequiredIfHasVariant($rules) {
        return request()->hasVariant ? 'nullable|'.$rules : 'required|'.$rules;
    }

    private function isNotRequiredIfHasVariant($rules) {
        return request()->hasVariant ? 'required|'.$rules : 'nullable|'.$rules;
    }

    public function messages()
    {
        return [
            'uuid.required' => 'Please provide the product uuid.',
            'productName.required' => 'Please provide the product name.',
            'url.required' => 'Please provide the product url.',
            'url.regex' => 'The url should not contain any white spaces.',
            'brandId.exists'=>'The selected brand does not exist',
            'brandId.integer'=>'The brandId must be a integer',
            'sortOrder.required' => 'The sort order is required.',
            'sortOrder.integer' => 'The sort order must be an integer.',
            'sortOrder.min' => 'The sort order must be at least 0.',
            'BestSeller'=>"The BestSeller field must be a boolean.",

            'originalPrice.required' => 'Please specify the original price of the product.',
            'originalPrice.numeric' => 'The original price must be a number.',
            'originalPrice.min' => 'The original price must be at least :min.',
            'specialPrice.numeric' => 'The special price must be a number.',
            'specialPrice.min' => 'The special price must be at least :min.',
            'specialPrice.lte' => 'The special price must be less than or equal to the original price.',
            'specialPriceStart.date' => 'The start date of the special price must be a valid date.',
            'specialPriceStart.after_or_equal' => 'The start date of the special price must be today or in the future.',
            'specialPriceStart.required_with' => 'The start date of the special price is required if a special price is set.',
            'specialPriceEnd.date' => 'The end date of the special price must be a valid date.',
            'specialPriceEnd.after' => 'The end date of the special price must be after the start date.',
            'specialPriceEnd.required_with' => 'The end date of the special price is required if a special price is set.',

            'sku.required' => 'Please provide the product SKU.',
            'description.required' => 'Please provide a description for the product.',
            'status.required' => 'Please specify the status of the product.',
            'status.boolean' => 'The status must be either true or false.',

            'saleStart.date' => 'The sale start date must be a valid date.',
            'saleStart.after_or_equal' => 'The sale start date must be today or in the future.',
            'saleEnd.date' => 'The sale end date of must be a valid date.',
            'saleEnd.after' => 'The sale end date of must be after the sale start date.',

            'quantity.required' => 'Please specify the quantity of the product.',
            'quantity.integer' => 'The quantity must be a whole number.',
            'quantity.min' => 'The quantity must be at least :min.',
            'inStock.required' => 'Please specify if the product is in stock.',
            'inStock.boolean' => 'The in stock field must be either true or false.',

            'categories.array' => 'Categories must be provided as an array.',
            'categories.*.exists' => 'One or more selected categories do not exist.',

            'tags.array' => 'Tags must be provided as an array.',
            'tags.*.exists' => 'One or more selected tags do not exist.',

            'files.baseImage.required' => 'Please provide the base image for your product.',
            'files.baseImage.exists' => 'The base image selected does not exist.',
            'files.additionalImage.*.exists' => 'One or more additional images selected do not exist.',
            'files.descriptionImage.*.exists' => 'One or more description images selected do not exist.',

            'options.required' => 'Please provide product options.',
            'options.*.name.required' => 'Please provide a name for the option.',
            'options.*.hasImage.required' => 'Please specify if the option has images.',
            'options.*.hasImage.boolean' => 'The hasImage field must be either true or false.',
            'options.*.values.required' => 'Please provide values for the option.',
            'options.*.values.*.optionName.required' => 'Please provide a name for the option value.',
            'options.*.values.*.optionName.unique' => 'This option value already exists for the selected option.',

            'options.*.values.*.files.required' => 'Please provide files for the option value.',

            'variants.required' => 'Please provide product variants.',
            'variants.*.name.required' => 'Please provide a name for the variant.',
            'variants.*.sku.required' => 'Please provide a SKU for the variant.',
            'variants.*.status.required' => 'Please specify the status of the variant.',
            'variants.*.status.boolean' => 'The status field must be either true or false.',
            'variants.*.originalPrice.required' => 'Please specify the original price of the variant.',
            'variants.*.originalPrice.numeric' => 'The original price must be a number.',
            'variants.*.originalPrice.min' => 'The original price must be at least :min.',
            'variants.*.specialPrice.numeric' => 'The special price must be a number.',
            'variants.*.specialPrice.min' => 'The special price must be at least :min.',
            'variants.*.specialPrice.lte' => 'The special price must be less than or equal to the original price.',
            'variants.*.quantity.required' => 'Please specify the quantity of the variant.',
            'variants.*.quantity.integer' => 'The quantity must be a whole number.',
            'variants.*.quantity.min' => 'The quantity must be at least :min.',
            'variants.*.inStock.required' => 'Please specify if the variant is in stock.',
            'variants.*.inStock.boolean' => 'The in stock field must be either true or false.',

            'meta.metaTitle.string' => 'The meta title must be a string.',
            'meta.keywords.string' => 'The meta keywords must be a string.',
            'meta.metaDescription.string' => 'The meta description must be a string.',

            'attributes.*.attributeId.exists' => 'One or more selected attributes do not exist.',
            'attributes.*.values.*.exists' => 'One or more selected attribute values do not exist.',

            'newFrom.date' => 'The New From field must be a valid date.',
            'newTo.date' => 'The New To field must be a valid date.',
            'newFrom.after_or_equal' => 'The New From date must be equal to or after today.',
            'newTo.after' => 'The New To date must be after the New From date.',
            'newTo.required_with' => 'The New To field is required when New From is present.',

            'relatedProducts.*.exists' => 'One or more selected related products do not exist.',
            'upSells.*.exists' => 'One or more selected up-sell products do not exist.',
            'crossSells.*.exists' => 'One or more selected cross-sell products do not exist.',

            'couponId.*.integer' => 'The coupon id must be a whole number.',
            'featureId.*.integer' => 'The feature id must be a whole number.',

            'specifications.array' => 'Specifications must be an array.',
            'specifications.max' => 'The specifications field must not have more than 6 items.',
            'specifications.*.string' => 'Each specification must be a string.',
            'specifications.*.min' => 'Each specification must be at least 3 characters long.',
            'specifications.*.max' => 'Each specification cannot exceed 255 characters.',

            'keySpecs.array' => 'Key specs must be an array',
            'keySpecs.*.key.required' => 'Each key spec must have a key',
            'keySpecs.*.key.string' => 'Key spec key must be a string',
            'keySpecs.*.value.required' => 'Each key spec must have a value array',
            'keySpecs.*.value.array' => 'Key spec value must be an array',
            'keySpecs.*.value.*.string' => 'Each value in key spec must be a string',
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