<?php

namespace Modules\Coupon\Service\User;

use Illuminate\Support\Facades\Cache;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Modules\Cart\App\Models\Cart;
use Modules\Cart\App\Models\GuestCart;
use Modules\Coupon\App\Models\Coupon;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

use Modules\Coupon\Checkers\CouponExists;
use Modules\Coupon\Checkers\MinimumSpend;
use Modules\Coupon\Checkers\ValidCoupon;
use Modules\Coupon\Checkers\CheckCartQuantity;
use Modules\Coupon\Checkers\UsageLimitPerCoupon;
use Modules\Coupon\Checkers\UsageLimitPerCustomer;
use Modules\Coupon\Checkers\ApplicableProducts;
use Modules\Coupon\Checkers\ExcludedProducts;
use Modules\Coupon\Checkers\StackableCoupon;
use Modules\Coupon\Checkers\RelatedCoupons;
use Modules\Coupon\Checkers\ExcludedCoupons;


class ApplyCouponService
{
    private $loggedInCheckers = [
        CouponExists::class,
        MinimumSpend::class,
        ValidCoupon::class,
        CheckCartQuantity::class,
        UsageLimitPerCoupon::class,
        UsageLimitPerCustomer::class,
        ApplicableProducts::class,
        ExcludedProducts::class,
        StackableCoupon::class, // Ensure these checkers use $coupon->type
        RelatedCoupons::class,
        ExcludedCoupons::class,
    ];

    private $nonLoggedInCheckers = [
        CouponExists::class,
        ValidCoupon::class,
        MinimumSpend::class,
        CheckCartQuantity::class,
        UsageLimitPerCoupon::class,
        ApplicableProducts::class,
        ExcludedProducts::class,
    ];

    /**
     * Detect cart and cart type from the request parameters
     *
     * @param \Illuminate\Http\Request $request
     * @return array The detected cart information [couponCode, cartType, cartIdentifier]
     */
    public function detectCartAndType(\Illuminate\Http\Request $request)
    {
        // Switch to user guard for correct authentication handling with Bearer tokens
        auth()->shouldUse('user');



        $couponCode = $request->get('couponCode');
        $cartType = $request->get('cartType');

        // Try multiple parameter names for cart identifier
        $cartIdentifier = $request->get('cartIdentifier') ?: $request->get('cartId') ?: $request->get('cart_id') ?: $request->get('guest_token');

        // For logged-in users, ALWAYS use the user's cart regardless of passed parameters
        if (auth()->check()) {
            $cartType = 'user';
            $cart = Cart::getForCurrentUser();
            if ($cart) {
                $cartIdentifier = $cart->uuid;
            } else {
                // Create a new cart for the user if none exists
                $cart = new Cart();
                $cart->user_id = auth()->id();
                $cart->uuid = \Illuminate\Support\Str::uuid()->toString();
                $cart->save();
                $cartIdentifier = $cart->uuid;
            }
        }
        // Only process guest token and other parameters if user is not logged in
        else {
            // Check headers for guest token
            $guestTokenHeader = $request->header('X-Guest-Token');
            if ($guestTokenHeader && !$cartIdentifier) {
                $cartIdentifier = $guestTokenHeader;
                $cartType = 'guest';
            }

            // Try to get cart info from JSON body if not found in request parameters
            if (!$cartIdentifier || !$cartType) {
                $jsonData = $request->json()->all();
                if (!empty($jsonData)) {
                    $cartIdentifier = $cartIdentifier ?: ($jsonData['cartIdentifier'] ?? $jsonData['cartId'] ?? $jsonData['cart_id'] ?? $jsonData['guest_token'] ?? null);
                    $cartType = $cartType ?: ($jsonData['cartType'] ?? null);

                    // If we have a guest token but no cart type, assume it's a guest cart
                    if (!$cartType && isset($jsonData['guest_token'])) {
                        $cartType = 'guest';
                    }
                }
            }

            // For guests with a token but no specified cart type
            if (!$cartType && $cartIdentifier) {
                // Check if this matches a guest token pattern (typically a UUID)
                if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $cartIdentifier)) {
                    $cartType = 'guest';
                }
            }
        }

        // If we still don't have a cartType, try to infer it
        if (!$cartType && $cartIdentifier) {
            // Try to find as user cart
            $userCart = Cart::where('uuid', $cartIdentifier)->first();
            if ($userCart) {
                $cartType = 'user';
            } else {
                // Try to find as guest cart
                $guestCart = GuestCart::where('guest_token', $cartIdentifier)->first();
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
            'cartIdentifier' => $cartIdentifier
        ];
    }

    function applyCoupon(string $couponCode, ?string $cartType = null, ?string $cartIdentifier = null)
    {
        try {
            DB::beginTransaction(); // Start transaction if applyCoupon makes changes (e.g., to cart)

            $coupon = Coupon::findByCode($couponCode);

            if (!$coupon) {
                throw new Exception('Coupon not found or not active.', ErrorCode::NOT_FOUND);
            }

            // Determine if this is a guest cart or user cart
            $isGuestCart = $cartType === 'guest';
            $cart = null;

            // For logged-in users, always use user cart regardless of cart type parameter
            if (auth()->check()) {
                $isGuestCart = false;

                // Get the current authenticated user
                $user = auth()->user();

                // First try to get the current user's cart
                $cart = Cart::getForCurrentUser();

                // If not found and a cart identifier was provided, try that
                if (!$cart && $cartIdentifier) {
                    // Try with exact match
                    $cart = Cart::where('uuid', $cartIdentifier)->first();

                    // If not found, try case-insensitive
                    if (!$cart) {
                        $cart = Cart::whereRaw('LOWER(uuid) = ?', [strtolower($cartIdentifier)])->first();
                    }

                    // If still not found and it's numeric, try by ID
                    if (!$cart && is_numeric($cartIdentifier)) {
                        $cart = Cart::find($cartIdentifier);
                    }

                    \Illuminate\Support\Facades\Log::info('Cart lookup by identifier', [
                        'cartIdentifier' => $cartIdentifier,
                        'cart_found' => $cart ? 'yes' : 'no'
                    ]);
                }

                if (!$cart) {
                    // Create a new cart for the user if none exists
                    $cart = new Cart();
                    $cart->user_id = auth()->id();
                    $cart->uuid = \Illuminate\Support\Str::uuid()->toString();
                    $cart->save();

                    \Illuminate\Support\Facades\Log::info('New cart created for user', [
                        'cart_id' => $cart->id,
                        'cart_uuid' => $cart->uuid,
                        'user_id' => $cart->user_id
                    ]);
                }
            }
            // For non-authenticated users, process as guest cart
            else {
                // If we have a cart identifier but no cart type, let's try to find what type of cart it is
                if ($cartIdentifier && !$cartType) {
                    // For unauthenticated users, assume guest cart
                    $guestCart = GuestCart::where('guest_token', $cartIdentifier)->first();
                    if ($guestCart) {
                        $isGuestCart = true;
                        $cart = $guestCart;
                    }
                } else if ($isGuestCart) {
                    // Get guest cart - try multiple ways to find it
                    $cart = GuestCart::where('guest_token', $cartIdentifier)->first();

                    // If not found, try case-insensitive search
                    if (!$cart && $cartIdentifier) {
                        $cart = GuestCart::whereRaw('LOWER(guest_token) = ?', [strtolower($cartIdentifier)])->first();
                    }

                    // If still not found, check all guest carts
                    if (!$cart) {
                        $allGuestCarts = GuestCart::all();

                        throw new Exception('Guest cart not found. Please add items to your cart before applying a coupon.', ErrorCode::NOT_FOUND);
                    }
                } else {
                    // For unauthenticated users without a cart, throw an error
                    throw new Exception('Guest cart not found. Please add items to your cart before applying a coupon.', ErrorCode::NOT_FOUND);
                }
            }



            // Determine proper checkers to use based on authentication state
            $isAuthenticated = !$isGuestCart;
            $checkers = $isAuthenticated ? $this->loggedInCheckers : $this->nonLoggedInCheckers;

            // The pipeline sends both coupon and cart to checkers
            $pipelineData = [
                'coupon' => $coupon,
                'cart' => $cart
            ];

            $result = resolve(Pipeline::class)
                ->send($pipelineData)
                ->through($checkers)
                ->thenReturn();

            // The coupon from the pipeline result might have been modified by checkers
            $validatedCoupon = $result['coupon'];

            // Apply the coupon to the cart
            $discountData = $cart->applyCoupon($validatedCoupon);

            DB::commit(); // Commit if all successful

            // Removed cache invalidation for real-time consistency

            $response = [
                'couponCode' => $validatedCoupon->code,
                'type' => $validatedCoupon->type,
                'value' => $validatedCoupon->value,
                'isPercent' => $validatedCoupon->isPercentageType(),
                'discountAmount' => $discountData['discountAmount'],
                'message' => 'Coupon applied successfully.',
            ];

            return $response;
        } catch (\Exception $exception) {
            DB::rollBack();

            // Rethrow custom exceptions with user-friendly messages
            if ($exception instanceof Exception) {
                throw $exception;
            }
            // For generic exceptions, throw a generic error
            throw new Exception('Could not apply coupon. Please try again. Error: ' . $exception->getMessage(), ErrorCode::BAD_REQUEST);
        }
    }

    /**
     * Calculates the discount amount based on coupon type and cart items.
     * This method might be better placed within the Cart model or a dedicated CartDiscountCalculator service.
     */
    private function calculateDiscountAmount(Coupon $coupon, $cart): float
    {
        $totalDiscountAmount = 0;
        // Ensure getApplicableItems filters correctly based on coupon's product restrictions
        $applicableItems = $this->getApplicableItems($coupon, $cart);

        if ($applicableItems->isEmpty() && ($coupon->isPercentageType() || $coupon->isFixedCartType())) {
            return 0;
        }

        switch ($coupon->type) {
            case Coupon::TYPE_PERCENTAGE:
                foreach ($applicableItems as $item) {
                    $itemPrice = $item->purchased_price * $item->quantity;
                    $itemDiscount = ($itemPrice * $coupon->value / 100);
                    $totalDiscountAmount += $itemDiscount;
                }
                // Apply overall max_discount for this coupon if it's set
                if (!is_null($coupon->max_discount) && $coupon->max_discount > 0 && $totalDiscountAmount > $coupon->max_discount) {
                    $totalDiscountAmount = $coupon->max_discount;
                }
                break;

            case Coupon::TYPE_FIXED_CART:
                // Apply fixed amount to the subtotal of applicable items
                $subtotalOfApplicableItems = $applicableItems->sum(fn($item) => $item->purchased_price * $item->quantity);
                // Discount cannot be more than the subtotal of items it applies to, or more than coupon value
                $totalDiscountAmount = min($coupon->value, $subtotalOfApplicableItems);
                break;

            case Coupon::TYPE_FREE_SHIPPING:
                // Free shipping coupons don't provide direct cart discount
                // They are handled separately during checkout/shipping calculation
                $totalDiscountAmount = 0;
                break;

            default:
                $totalDiscountAmount = 0;
                break;
        }
        return round($totalDiscountAmount, 2); // Round to 2 decimal places
    }

    /**
     * Filters cart items to which the coupon applies.
     */
    private function getApplicableItems(Coupon $coupon, $cart)
    {
        // If coupon has no specific product inclusions/exclusions, it applies to all items.
        $hasProductInclusions = $coupon->products()->exists();
        $hasProductExclusions = $coupon->exclude()->exists();

        if (!$hasProductInclusions && !$hasProductExclusions) {
            return $cart->items;
        }

        $includedProductIds = $hasProductInclusions ? $coupon->products()->pluck('products.id') : collect();
        $excludedProductIds = $hasProductExclusions ? $coupon->exclude()->pluck('products.id') : collect();

        return $cart->items->filter(function($item) use ($includedProductIds, $excludedProductIds, $hasProductInclusions) {
            // Skip non-product items
            if (!property_exists($item, 'product_id') || is_null($item->product_id)) {
                return false;
            }

            // Exclude products in the excluded list
            if ($excludedProductIds->isNotEmpty() && $excludedProductIds->contains($item->product_id)) {
                return false;
            }

            // If there are specific included products, only include those
            if ($hasProductInclusions) {
                return $includedProductIds->contains($item->product_id);
            }

            // No specific inclusions but passed exclusion check
            return true;
        });
    }
}
