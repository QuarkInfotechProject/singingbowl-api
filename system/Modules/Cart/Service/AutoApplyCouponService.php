<?php

namespace Modules\Cart\Service;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Log;
use Modules\Cart\App\Models\Cart;
use Modules\Cart\App\Models\GuestCart;
use Modules\Coupon\App\Models\Coupon;
use Modules\Coupon\Checkers\ValidCoupon;
use Modules\Coupon\Checkers\MinimumSpend;
use Modules\Coupon\Checkers\CheckCartQuantity;
use Modules\Coupon\Checkers\ApplicableProducts;
use Modules\Coupon\Checkers\ExcludedProducts;

class AutoApplyCouponService
{
    /**
     * Checkers to run for auto-apply validation.
     * These are a subset of full checkers - we skip usage limits for auto-apply.
     */
    private array $checkers = [
        ValidCoupon::class,
        MinimumSpend::class,
        CheckCartQuantity::class,
        ApplicableProducts::class,
        ExcludedProducts::class,
    ];

    /**
     * Apply eligible auto-apply coupons to the cart.
     *
     * @param Cart|GuestCart $cart
     * @return void
     */
    public function applyEligibleCoupons($cart): void
    {
        if (!$cart || $cart->items->isEmpty()) {
            return;
        }

        // Get all active, auto-apply coupons
        $autoApplyCoupons = $this->getAutoApplyCoupons();

        if ($autoApplyCoupons->isEmpty()) {
            return;
        }

        // Get currently applied coupon codes
        $appliedCouponCodes = $cart->coupons->pluck('code')->toArray();

        foreach ($autoApplyCoupons as $coupon) {
            // Skip if already applied
            if (in_array($coupon->code, $appliedCouponCodes)) {
                continue;
            }

            // Try to apply this coupon
            $this->tryApplyCoupon($cart, $coupon);
        }
    }

    /**
     * Get all eligible auto-apply coupons.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getAutoApplyCoupons()
    {
        $today = today();

        return Coupon::where('apply_automatically', true)
            ->where('is_active', true)
            ->where(function ($query) use ($today) {
                // Start date is null or in the past/today
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                // End date is null or in the future/today
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            })
            ->get();
    }

    /**
     * Try to apply a coupon to the cart through the validation pipeline.
     *
     * @param Cart|GuestCart $cart
     * @param Coupon $coupon
     * @return bool
     */
    private function tryApplyCoupon($cart, Coupon $coupon): bool
    {
        try {
            // Load relationships needed for checkers
            $coupon->load(['products', 'exclude']);

            $pipelineData = [
                'coupon' => $coupon,
                'cart' => $cart
            ];

            // Run through validation pipeline
            $result = resolve(Pipeline::class)
                ->send($pipelineData)
                ->through($this->checkers)
                ->thenReturn();

            // If we get here, validation passed - apply the coupon
            $cart->applyCoupon($coupon);
            
            Log::info('Auto-applied coupon', [
                'coupon_code' => $coupon->code,
                'cart_id' => $cart->id ?? $cart->guest_token ?? 'unknown'
            ]);

            return true;
        } catch (\Exception $e) {
            // Coupon didn't pass validation - this is expected, not an error
            Log::debug('Auto-apply coupon skipped', [
                'coupon_code' => $coupon->code,
                'reason' => $e->getMessage()
            ]);

            return false;
        }
    }
}
