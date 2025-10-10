<?php

namespace Modules\Coupon\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Coupon\App\Models\Coupon;
use Modules\Order\App\Rules\ValidPaymentMethod;

class CouponCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $couponType = $this->input('type');

        $this->merge([
            'isActive' => $this->boolean('isActive'),
            'isPublic' => $this->boolean('isPublic'),
            'isBulkOffer' => $this->boolean('isBulkOffer'),
            'applyAutomatically' => $this->boolean('applyAutomatically'),
            'individualUse' => $this->boolean('individualUse'),
            'value' => $this->filled('value') ? $this->input('value') : null,
            'maxDiscount' => $this->filled('maxDiscount') ? $this->input('maxDiscount') : null,
            'minimumSpend' => $this->filled('minimumSpend') ? $this->input('minimumSpend') : null,
            'usageLimitPerCoupon' => $this->filled('usageLimitPerCoupon') ? $this->input('usageLimitPerCoupon') : null,
            'usageLimitPerCustomer' => $this->filled('usageLimitPerCustomer') ? $this->input('usageLimitPerCustomer') : null,
            'minQuantity' => $this->filled('minQuantity') ? $this->input('minQuantity') : null,
        ]);
    }

    public function rules(): array
    {
        $couponType = $this->input('type');

        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'code' => ['required', 'string', 'min:2', 'max:50', 'unique:coupons,code'],
            'type' => ['required', 'string', Rule::in(array_keys(Coupon::getTypes()))],

            // These are derived, but good to ensure they are boolean if present in request data
            // 'isPercent' => ['sometimes', 'boolean'], // No longer a direct input field expected to determine type
            // 'freeShipping' => ['sometimes', 'boolean'], // Similarly, primary driven by 'type'

            'value' => [
                // Value required for percentage and fixed_cart, not for free_shipping
                Rule::requiredIf(fn () => in_array($couponType, [
                    Coupon::TYPE_PERCENTAGE,
                    Coupon::TYPE_FIXED_CART, // Changed from TYPE_FIXED_PRODUCT
                ])),
                'nullable',
                'numeric',
                'min:0',
                'max:999999999999999999', // Max for DB decimal(18) (whole numbers)
                function ($attribute, $value, $fail) use ($couponType) {
                    if ($couponType === Coupon::TYPE_PERCENTAGE && $value > 100) {
                        $fail('The coupon value cannot be greater than 100% for percentage coupons.');
                    }
                },
            ],
            'maxDiscount' => [
                'nullable',
                Rule::requiredIf(fn() => $couponType === Coupon::TYPE_PERCENTAGE && $this->filled('value')),
                'numeric',
                'min:0',
                'max:999999.99', // For DB decimal(8,2)
            ],

            'startDate' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today'],
            'endDate' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:startDate'],
            'isActive' => ['required', 'boolean'],
            'isPublic' => ['required', 'boolean'],
            'isBulkOffer' => ['required', 'boolean'],
            'minimumSpend' => [
                'nullable',
                // Generally, minimum spend is not applicable if the primary type is free shipping itself.
                // However, a percentage/fixed coupon might ALSO offer free shipping if a minimum is met.
                // The 'freeShipping' boolean (derived if type is free_shipping, or from input otherwise)
                // can be used in conjunction with type.
                // For simplicity: require if not purely a free_shipping type.
                Rule::requiredIf(fn() => $couponType !== Coupon::TYPE_FREE_SHIPPING),
                'numeric',
                'min:0',
                'max:999999999999999999',
            ],
            'products' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    $excludeProducts = $this->input('excludeProducts', []);
                    if (is_array($value) && is_array($excludeProducts)) {
                        $duplicates = array_intersect($value, $excludeProducts);
                        if (!empty($duplicates)) {
                            $fail('Products cannot be in both include and exclude lists. Duplicate products: ' . implode(', ', $duplicates));
                        }
                    }
                }
            ],
            'products.*' => ['required', 'string', 'exists:products,uuid'],
            'excludeProducts' => ['nullable', 'array'],
            'excludeProducts.*' => ['required', 'string', 'exists:products,uuid'],
            'usageLimitPerCoupon' => ['nullable', 'integer', 'min:1', 'max:2147483647'],
            'usageLimitPerCustomer' => ['nullable', 'integer', 'min:1', 'max:2147483647'],
            'minQuantity' => ['nullable', 'integer', 'min:1'],
            'applyAutomatically' => ['required', 'boolean'],
            'individualUse' => ['required', 'boolean'],
            'paymentMethods' => ['nullable', 'array'],
            'paymentMethods.*' => ['required', 'string', new ValidPaymentMethod],
            'relatedCoupons' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    $excludedCoupons = $this->input('excludedCoupons', []);
                    if (is_array($value) && is_array($excludedCoupons)) {
                        $duplicates = array_intersect($value, $excludedCoupons);
                        if (!empty($duplicates)) {
                            $fail('Coupons cannot be in both related and excluded lists. Duplicate coupons: ' . implode(', ', $duplicates));
                        }
                    }
                }
            ],
            'relatedCoupons.*' => ['required', 'integer', 'exists:coupons,id'],
            'excludedCoupons' => ['nullable', 'array'],
            'excludedCoupons.*' => ['required', 'integer', 'exists:coupons,id'],
        ];
    }

    public function messages()
    {
        return array_merge(parent::messages(), [
            'name.required' => 'Please enter a name or description for the coupon.',
            'code.required' => 'Please enter a unique code for the coupon.',
            'code.unique' => 'This coupon code has already been used.',
            'type.required' => 'Please select a coupon type.',
            'type.in' => 'The selected coupon type is invalid.',
            'value.required' => 'A coupon value is required for percentage or fixed cart coupon types.',
            'maxDiscount.required' => 'A maximum discount is required for percentage coupons with a value.',
            'minimumSpend.required' => 'Minimum spend is required unless the coupon is for free shipping only.',
            // Add other messages as needed
        ]);
    }
}
