<?php

namespace Modules\Coupon\Service\User;

use Modules\Cart\App\Models\Cart;
use Modules\Cart\App\Models\GuestCart;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class RemoveCouponService
{
    function removeCoupon(array $data)
    {
        if (!isset($data['cartId'])) {
            throw new Exception('Cart identifier is required.', ErrorCode::BAD_REQUEST);
        }

        if (!isset($data['couponCode'])) {
            throw new Exception('Coupon code is required.', ErrorCode::BAD_REQUEST);
        }

        $cartId = $data['cartId'];
        $couponCode = $data['couponCode'];
        $isGuestCart = isset($data['cartType']) && $data['cartType'] === 'guest';        // For logged-in users, always use user cart regardless of cart type parameter
        if (auth()->check()) {
            $isGuestCart = false;

            // Get the current authenticated user
            $user = auth()->user();

            $cart = Cart::getForCurrentUser();

            if (!$cart && $cartId) {
                // Try with exact match
                $cart = Cart::where('uuid', $cartId)->first();

                // If not found, try case-insensitive
                if (!$cart) {
                    $cart = Cart::whereRaw('LOWER(uuid) = ?', [strtolower($cartId)])->first();
                }

                // If still not found and it's numeric, try by ID
                if (!$cart && is_numeric($cartId)) {
                    $cart = Cart::find($cartId);
                }


            }

            if (!$cart) {
                // Create a new cart for the user if none exists
                $cart = new Cart();
                $cart->user_id = auth()->id();
                $cart->uuid = \Illuminate\Support\Str::uuid()->toString();
                $cart->save();


            }
        }
        // For non-authenticated users, handle as guest cart
        else if ($isGuestCart) {
            // Handle guest cart
            $cart = GuestCart::where('guest_token', $cartId)->first();
        }
        // Fallback for non-authenticated users with non-guest cart (should not happen)
        else {
            throw new Exception('Cart not found.', ErrorCode::NOT_FOUND);
        }

        $coupon = $cart->coupons
            ->firstWhere('code', $couponCode);

        if (!$coupon) {
            throw new Exception("Coupon not found in this cart.", ErrorCode::NOT_FOUND);
        }

        $cart->coupons()->detach($coupon->id);

        // Recalculate cart totals after removing coupon
        $cart->recalculateTotal();

        // Removed cache invalidation for real-time consistency
    }

    /**
     * Detect cart and cart type from the request parameters
     *
     * @param \Illuminate\Http\Request $request
     * @return array The detected cart information [couponCode, cartType, cartId]
     */
    public function detectCartAndType(\Illuminate\Http\Request $request)
    {
        // Switch to user guard for correct authentication handling with Bearer tokens
        auth()->shouldUse('user');

        $couponCode = $request->get('couponCode');
        $cartType = $request->get('cartType');

        // Try multiple parameter names for cart identifier
        $cartId = $request->get('cartIdentifier') ?: $request->get('cartId') ?: $request->get('cart_id') ?: $request->get('guest_token');

        // For logged-in users, ALWAYS use the user's cart regardless of passed parameters
        if (auth()->check()) {
            $cartType = 'user';
            $cart = Cart::getForCurrentUser();
            if ($cart) {
                $cartId = $cart->uuid;
            } else {
                // Create a new cart for the user if none exists
                $cart = new Cart();
                $cart->user_id = auth()->id();
                $cart->uuid = \Illuminate\Support\Str::uuid()->toString();
                $cart->save();
                $cartId = $cart->uuid;
            }
        }
        // Only process guest token and other parameters if user is not logged in
        else {
            // Check headers for guest token
            $guestTokenHeader = $request->header('X-Guest-Token');
            if ($guestTokenHeader && !$cartId) {
                $cartId = $guestTokenHeader;
                $cartType = 'guest';
            }

            // Try to get cart info from JSON body if not found in request parameters
            if (!$cartId || !$cartType) {
                $jsonData = $request->json()->all();
                if (!empty($jsonData)) {
                    $cartId = $cartId ?: ($jsonData['cartIdentifier'] ?? $jsonData['cartId'] ?? $jsonData['cart_id'] ?? $jsonData['guest_token'] ?? null);
                    $cartType = $cartType ?: ($jsonData['cartType'] ?? null);

                    // If we have a guest token but no cart type, assume it's a guest cart
                    if (!$cartType && isset($jsonData['guest_token'])) {
                        $cartType = 'guest';
                    }
                }
            }

            // For guests with a token but no specified cart type
            if (!$cartType && $cartId) {
                // Check if this matches a guest token pattern (typically a UUID)
                if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $cartId)) {
                    $cartType = 'guest';
                }
            }
        }

        // If we still don't have a cartType, try to infer it
        if (!$cartType && $cartId) {
            // Try to find as user cart
            $userCart = Cart::where('uuid', $cartId)->first();
            if ($userCart) {
                $cartType = 'user';
            } else {
                // Try to find as guest cart
                $guestCart = GuestCart::where('guest_token', $cartId)->first();
                if ($guestCart) {
                    $cartType = 'guest';
                }
            }
        }

        // Extract Bearer token if present but not already processed
        $bearerToken = $request->bearerToken();

        return [
            'couponCode' => $couponCode,
            'cartType' => $cartType,
            'cartId' => $cartId
        ];
    }
}
