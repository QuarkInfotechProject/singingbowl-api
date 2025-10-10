<?php

namespace Modules\User\Service;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Modules\Cart\App\Models\Cart;
use Modules\Cart\App\Models\CartItem;
use Modules\Cart\App\Models\GuestCart;
use Modules\User\App\Models\User;

class MergeCartsService
{
    /**
     * Merge guest cart into user's cart upon login/registration.
     *
     * @param string $guestToken
     * @param User $user
     * @return Cart
     */
    public function execute(string $guestToken, User $user): Cart
    {
        $guestCart = GuestCart::with('items', 'coupons')->where('guest_token', $guestToken)->first();
        if (!$guestCart || $guestCart->items->isEmpty()) {
            // No guest cart or empty guest cart, just return user's existing or new cart
            return Cart::firstOrCreate(['user_id' => $user->id], ['uuid' => Str::uuid()->toString()]);
        }

        $userCart = Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['uuid' => Str::uuid()->toString(), 'user_agent' => request()->userAgent()]
        );

        DB::transaction(function () use ($guestCart, $userCart) {
            foreach ($guestCart->items as $guestItem) {
                $userCartItem = $userCart->items()
                                        ->where('product_id', $guestItem->product_id)
                                        ->where('variant_id', $guestItem->variant_id)
                                        ->first();

                if ($userCartItem) {
                    $userCartItem->quantity += $guestItem->quantity;
                    // Potentially re-evaluate price if promotions/user-specific prices apply
                    $userCartItem->save();
                } else {
                    CartItem::create([
                        'cart_id'         => $userCart->id,
                        'product_id'      => $guestItem->product_id,
                        'variant_id'      => $guestItem->variant_id,
                        'quantity'        => $guestItem->quantity,
                        'purchased_price' => $guestItem->purchased_price, // Or recalculate based on user context
                        'variant_options' => $guestItem->variant_options, // Assuming structure is compatible
                    ]);
                }
            }

            // Apply coupons from guest cart to user cart
            if ($guestCart->coupons->isNotEmpty()) {
                foreach ($guestCart->coupons as $coupon) {
                    $userCart->applyCoupon($coupon);
                }
            }

            $guestCart->delete(); // Delete guest cart after merging

            // Removed cache invalidation for real-time consistency
        });

        return $userCart->load('items.product', 'items.variant');
    }
}