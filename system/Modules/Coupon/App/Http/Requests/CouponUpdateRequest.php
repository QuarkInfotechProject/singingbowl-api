<?php

namespace Modules\Coupon\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Coupon\App\Models\Coupon; // Import your Coupon model
use Modules\Order\App\Rules\ValidPaymentMethod;

class CouponUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust authorization as needed
    }

    /**
     * Prepare the data for validation.
     */
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

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Get coupon ID from route parameter (e.g., /coupons/{coupon}) or from input 'id'
        $couponId = $this->route('coupon') ? $this->route('coupon')->id : $this->input('id');
        $couponType = $this->input('type');

        return [
            'id' => ['required', 'integer', 'exists:coupons,id'], // Ensure the ID from input exists
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'code' => [
                'required',
                'string',
                'min:2',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($couponId),
            ],
            'type' => ['required', 'string', Rule::in(array_keys(Coupon::getTypes()))],


            'value' => [
                Rule::requiredIf(fn () => in_array($couponType, [
                    Coupon::TYPE_PERCENTAGE,
                    Coupon::TYPE_FIXED_CART,
                ])),
                'nullable',
                'numeric',
                'min:0',
                'max:999999999999999999', // Adjust if value can be decimal
                function ($attribute, $value, $fail) use ($couponType) {
                    if ($couponType === Coupon::TYPE_PERCENTAGE && $value > 100) {
                        $fail('The coupon value cannot be greater than 100% for percentage coupons.');
                    }
                },
            ],
            'maxDiscount' => [ // DTO: maxDiscount, Model: max_discount
                'nullable',
                Rule::requiredIf(fn() => $couponType === Coupon::TYPE_PERCENTAGE && $this->filled('value')),
                'numeric',
                'min:0',
                'max:999999.99', // For decimal(8,2)
            ],

            // For updates, 'after_or_equal:today' might be too restrictive if editing an old coupon's start date
            'startDate' => ['nullable', 'date_format:Y-m-d'],
            'endDate' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:startDate'],

            'isActive' => ['required', 'boolean'],
            'isPublic' => ['required', 'boolean'],
            'isBulkOffer' => ['required', 'boolean'],

            'minimumSpend' => [
                'nullable',
                Rule::requiredIf(fn() => $couponType !== Coupon::TYPE_FREE_SHIPPING),
                'numeric',
                'min:0',
                'max:999999999999999999', // Adjust if decimal
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
            'minQuantity' => [
                'nullable',
                'integer',
                'min:1',
                // Rule::requiredIf(fn() => $couponType === Coupon::TYPE_BUY_X_GET_Y),
            ],
            'applyAutomatically' => ['required', 'boolean'],
            'individualUse' => ['required', 'boolean'], // DTO: individualUse

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
            'relatedCoupons.*' => ['required', 'integer', 'exists:coupons,id', Rule::notIn([$couponId])], // Cannot relate to itself

            'excludedCoupons' => ['nullable', 'array'],
            'excludedCoupons.*' => ['required', 'integer', 'exists:coupons,id', Rule::notIn([$couponId])], // Cannot exclude itself
        ];
    }

    public function messages()
    {
        // Similar to CouponCreateRequest, with adjustments for update context
        return array_merge(parent::messages(), [
            'id.required' => 'The coupon ID is required for an update.',
            'id.exists' => 'The selected coupon for update does not exist.',
            'name.required' => 'Please enter a name or description for the coupon.',
            'code.required' => 'Please enter a unique code for the coupon.',
            'code.unique' => 'This coupon code is already used by another coupon.',
            'type.required' => 'Please select a coupon type.',
            'type.in' => 'The selected coupon type is invalid.',
            'value.required' => 'A coupon value is required for the selected coupon type.',
            'maxDiscount.required' => 'A maximum discount is required for percentage coupons with a value.',
            'relatedCoupons.*.not_in' => 'A coupon cannot be related to or excluded from itself.',
            'excludedCoupons.*.not_in' => 'A coupon cannot be related to or excluded from itself.',
            'startDate.date_format' => 'The start date must be in YYYY-MM-DD format.',
            'endDate.date_format' => 'The end date must be in YYYY-MM-DD format.',
        ]);
    }
}
