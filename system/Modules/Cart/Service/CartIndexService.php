<?php

namespace Modules\Cart\Service;

use Modules\Product\App\Models\ProductOptionValue;
use Modules\Cart\Service\CartRepository;
use Modules\Cart\Service\AutoApplyCouponService;
use Modules\Shared\Exception\Exception;
use Modules\DeliveryCharge\App\Models\DeliveryCharge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartIndexService
{
    public function __construct(
        private CartRepository $cartRepository,
        private AutoApplyCouponService $autoApplyCouponService
    ) {
    }

    /**
     * Get cart contents based on cart type and identifier.
     */
    public function index(string $cartType, $cartIdentifier)
    {
        try {
            $cart = $this->cartRepository->getCart($cartType, $cartIdentifier);

            if (!$cart) {
                return $this->getEmptyCartStructure($cartType, $cartIdentifier);
            }

            if (!$cart->relationLoaded('items')) {
                $cart->load([
                    'items',
                    'items.product.categories',
                    'items.product.files',
                    'items.variant',
                    'items.variant.optionValues',
                    'items.variant.optionValues.option',
                    'items.variant.optionValues.files',
                    'coupons'
                ]);
            }

            // Auto-apply eligible coupons before calculating totals
            $this->autoApplyCouponService->applyEligibleCoupons($cart);
            
            // Reload coupons after auto-apply
            $cart->load('coupons');

            $now = now();

            $cartItems = $this->mapCartItemsOptimized($cart, $now);
            $total = $cartItems->sum('lineTotal');

            // Get original shipping cost before any discounts
            $originalShippingCost = 0;
            $deliveryCharge = DeliveryCharge::first();
            if ($deliveryCharge) {
                $originalShippingCost = (float) $deliveryCharge->delivery_charge;
            }

            // Process coupons with original shipping cost for free shipping discount display
            $coupons = $this->getAppliedCouponsOptimized($cart->coupons ?? collect([]), $originalShippingCost);

            // Calculate item discounts (excluding free shipping discount from subtotal calculation)
            $itemDiscount = $coupons->filter(function ($coupon) {
                return ($coupon['type'] ?? '') !== 'free_shipping';
            })->sum('discountAmount');
            $itemDiscount = min($itemDiscount, $total);

            // --- SIMPLIFIED SHIPPING CALCULATION ---
            $shippingCost = $originalShippingCost;
            $shippingType = $originalShippingCost > 0 ? 'Flat Rate' : 'No Delivery Charge';
            // --- END SHIPPING CALCULATION ---

            // Check for free shipping coupon and apply shipping discount
            $shippingDiscount = 0;
            $hasFreeShipping = false;
            if ($cart->coupons && $cart->coupons->isNotEmpty()) {
                foreach ($cart->coupons as $coupon) {
                    if ($coupon->type === 'free_shipping') {
                        $hasFreeShipping = true;
                        $shippingDiscount = $originalShippingCost;
                        break;
                    }
                }
            }

            // If free shipping coupon is applied, zero out shipping cost
            if ($hasFreeShipping) {
                $shippingCost = 0;
            }

            // Total discount includes both item discount and shipping discount for display
            $totalDiscount = $itemDiscount + $shippingDiscount;

            // Update Grand Total: (subtotal - item discounts) + final shipping
            $grandTotal = ($total - $itemDiscount) + $shippingCost;

            $result = [
                'items' => $cartItems,
                'total' => $total,
                'count' => $cartItems->count(),
                'grand_total' => $grandTotal,
                'total_discount' => $totalDiscount,
                'shipping_discount' => $shippingDiscount,
                'coupons' => $coupons->toArray(),

                // Add Shipping info to response
                'shipping_charge' => $shippingCost,
                'shipping_type' => $shippingType,
            ];

            if ($cartType === 'user') {
                $result['cart_id'] = $cartItems->isNotEmpty() ? $cart->uuid : null;
            } else {
                $result['cart_id'] = $cartIdentifier;
            }

            return $result;
        } catch (\Exception $exception) {
            Log::error('Cart Index Error: ' . $exception->getMessage());
            // Return empty structure on error to prevent frontend crash
            return $this->getEmptyCartStructure($cartType, $cartIdentifier);
        }
    }

    /**
     * Get variant options from different possible sources
     */
    private function getVariantOptions($cartItem, $variant)
    {
        $variantOptions = $cartItem->variant_options ?? [];

        if (is_string($variantOptions)) {
            $variantOptions = json_decode($variantOptions, true) ?? [];
        }

        if (!empty($variantOptions) && isset($variantOptions[0]) && isset($variantOptions[0]['option_name'])) {
            return $variantOptions;
        }

        if (!empty($variantOptions) && !isset($variantOptions[0])) {
            $formattedOptions = [];
            foreach ($variantOptions as $optionName => $optionValue) {
                $isColor = strtolower($optionName) === 'color';
                $formattedOptions[] = [
                    'option_name' => $optionName,
                    'value_name' => $optionValue,
                    'value_data' => '',
                    'is_color' => $isColor,
                ];
            }
            $variantOptions = $formattedOptions;
        }

        if (empty($variantOptions) && $variant && $variant->optionValues) {
            $variantOptions = $variant->optionValues->map(function($optionValue) {
                $isColor = false;
                if ($optionValue->option) {
                    $isColor = strtolower($optionValue->option->name) === 'color' ||
                             strtolower($optionValue->option->label) === 'color';
                }

                return [
                    'option_name' => $optionValue->option ? $optionValue->option->name : '',
                    'value_name' => $optionValue->value,
                    'value_data' => $optionValue->data ?? '',
                    'value_id' => $optionValue->id,
                    'is_color' => $isColor,
                ];
            })->toArray();
        }

        return $variantOptions;
    }

    /**
     * Format variant options for display
     */
    private function formatVariantOptions($variantOptions, $variant)
    {
        $formattedOptions = [];

        if (!empty($variantOptions)) {
            foreach ($variantOptions as $option) {
                $formattedOptions[] = [
                    'name' => $option['option_name'] ?? '',
                    'value' => $option['value_name'] ?? '',
                    'data' => $option['value_data'] ?? '',
                    'is_color' => $option['is_color'] ?? false,
                ];
            }
        }
        else if ($variant && isset($variant->optionValues) && $variant->optionValues->isNotEmpty() && empty($formattedOptions)) {
            foreach ($variant->optionValues as $optionValue) {
                $isColor = false;
                if (isset($optionValue->option)) {
                    $isColor = strtolower($optionValue->option->name) === 'color' ||
                             strtolower($optionValue->option->label) === 'color';
                }

                $formattedOptions[] = [
                    'name' => $optionValue->option ? $optionValue->option->name : '',
                    'value' => $optionValue->value,
                    'data' => $optionValue->data ?? '',
                    'is_color' => $isColor,
                ];
            }
        }

        return $formattedOptions;
    }

    private function getBaseImageFromProduct($product)
    {
        if (!$product) {
            return null;
        }

        if (!isset($product->files) || !$product->files) {
            return null;
        }

        if (method_exists($product, 'filterFiles')) {
            $file = $product->filterFiles('baseImage')->first();
            if (!$file) {
                $file = $product->filterFiles('additionalImage')->first();
            }
            return $file ? $file->url : null;
        }

        if ($product->files->isNotEmpty()) {
            return $product->files->first()->url;
        }

        return null;
    }

    /**
     * Get applied coupons (kept for backward compatibility).
     * @deprecated Use getAppliedCouponsOptimized instead
     */
    private function getAppliedCoupons($coupons)
    {
        return $this->getAppliedCouponsOptimized($coupons)->toArray();
    }

    /**
     * Get empty cart structure for consistent response format.
     */
    private function getEmptyCartStructure(string $cartType, $cartIdentifier)
    {
        return [
            'items' => [],
            'total' => 0,
            'count' => 0,
            'grand_total' => 0,
            'total_discount' => 0,
            'shipping_charge' => 0,
            'coupons' => [],
            'cart_id' => $cartType === 'guest' ? $cartIdentifier : null
        ];
    }

    /**
     * Optimized cart items mapping with pre-computed values.
     */
    private function mapCartItemsOptimized($cart, $now)
    {
        $optionValueIds = collect($cart->items)
            ->filter(fn($item) => $item->variant && !empty($this->getVariantOptions($item, $item->variant)))
            ->flatMap(function($item) {
                $options = $this->getVariantOptions($item, $item->variant);
                return collect($options)->pluck('value_id')->filter();
            })
            ->unique()
            ->toArray();

        $optionValuesWithFiles = [];
        if (!empty($optionValueIds)) {
            $optionValuesWithFiles = ProductOptionValue::with('files')
                ->whereIn('id', $optionValueIds)
                ->get()
                ->keyBy('id');
        }

        return collect($cart->items)->map(function ($cartItem) use ($now, $optionValuesWithFiles) {
            $product = $cartItem->product;
            $variant = $cartItem->variant;

            $unitPrice = ($cartItem->variant ?? $cartItem->product)->current_price;
            $lineTotal = $cartItem->quantity * $unitPrice;

            $variantOptions = $this->getVariantOptions($cartItem, $variant);
            $baseImage = $this->getItemImageOptimized($product, $variant, $variantOptions, $optionValuesWithFiles);
            $formattedOptions = $this->formatVariantOptions($variantOptions, $variant);

            $displayOriginalPrice = $variant ? $variant->original_price : $product->original_price;

            $cartId = null;
            if (isset($cartItem->cart)) {
                $cartId = $cartItem->cart->uuid;
            } elseif (isset($cartItem->guestCart)) {
                $cartId = $cartItem->guestCart->guest_token;
            }

            return [
                'cartId' => $cartId,
                'id' => $cartItem->id,
                'category' => $product->categories->first() ? $product->categories->first()->name : 'Unknown',
                'productName' => $product->product_name,
                'slug' => $product->slug,
                'baseImage' => $baseImage,
                'options' => $formattedOptions,
                'originalPrice' => $displayOriginalPrice,
                'unitPrice' => $unitPrice,
                'lineTotal' => $lineTotal,
                'quantity' => $cartItem->quantity,
                'weight' => $product->weight,
            ];
        });
    }

    /**
     * Optimized image retrieval using pre-loaded option values.
     */
    private function getItemImageOptimized($product, $variant, $variantOptions, $optionValuesWithFiles)
    {
        if ($variant && !empty($variantOptions)) {
            $colorOption = collect($variantOptions)->first(function ($option) {
                return ($option['is_color'] ?? false) ||
                        strtolower($option['option_name'] ?? '') === 'color';
            });

            if ($colorOption && isset($colorOption['value_id']) && isset($optionValuesWithFiles[$colorOption['value_id']])) {
                $optionValue = $optionValuesWithFiles[$colorOption['value_id']];
                if ($optionValue->files && $optionValue->files->isNotEmpty()) {
                    return $optionValue->files->first()->url;
                }
            }

            if ($colorOption && $variant->files && $variant->files->isNotEmpty()) {
                return $variant->files->first()->url;
            }
        }

        if ($variant && isset($variant->files) && $variant->files->isNotEmpty()) {
            return $variant->files->first()->url;
        }

        return $this->getBaseImageFromProduct($product);
    }

    /**
     * Optimized coupon processing returning a collection.
     */
    private function getAppliedCouponsOptimized($coupons, $originalShippingCost = 0)
    {
        if (!$coupons || $coupons->isEmpty()) {
            return collect([]);
        }

        return $coupons->map(function ($coupon) use ($originalShippingCost) {
            // For free shipping coupons, the discount amount is the original shipping cost
            $discountAmount = $coupon->type === 'free_shipping' 
                ? $originalShippingCost 
                : (float) $coupon->pivot->discount_amount;

            return [
                'code' => $coupon->code,
                'name' => $coupon->name,
                'type' => $coupon->type,
                'discountAmount' => $discountAmount,
                'isAutoApplied' => (bool) $coupon->apply_automatically
            ];
        });
    }
}
