<?php

namespace Modules\Cart\Service;

use Modules\Cart\App\Models\Cart;
use Modules\Cart\App\Models\CartItem;
use Modules\Cart\App\Models\GuestCart;
use Modules\Cart\App\Models\GuestCartItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartRepository
{
    /**
     * Get a cart based on type and identifier.
     */
    public function getCart(string $cartType, $identifier)
    {
        if ($cartType === 'user') {
            return $this->getUserCart($identifier);
        } else {
            return $this->getGuestCart($identifier);
        }
    }

    /**
     * Get a user cart with all relationships loaded.
     */
    private function getUserCart($userId)
    {
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            return Cart::getWithAllRelations($cart->id);
        }

        return null;
    }

    /**
     * Get a guest cart with all items and related product data.
     */
    private function getGuestCart($guestToken)
    {
        $cart = GuestCart::where('guest_token', $guestToken)->first();

        if ($cart) {
            return GuestCart::getWithAllRelations($cart->id);
        }

        return null;
    }

    /**
     * Create or get a cart for a user.
     */
    public function createOrGetUserCart($userId, $userAgent = null)
    {
        return DB::transaction(function () use ($userId, $userAgent) {
            $cart = Cart::where('user_id', $userId)->first();

            if (!$cart) {
                $cart = new Cart();
                $cart->uuid = Str::uuid();
                $cart->user_id = $userId;
                $cart->user_agent = $userAgent;
                $cart->save();
            }

            return $cart;
        });
    }

    /**
     * Create or get a cart for a guest.
     */
    public function createOrGetGuestCart($guestToken)
    {
        return DB::transaction(function () use ($guestToken) {
            $guestCart = GuestCart::where('guest_token', $guestToken)->first();

            if (!$guestCart) {
                $guestCart = new GuestCart();
                $guestCart->guest_token = $guestToken;
                $guestCart->save();
            }

            return $guestCart;
        });
    }

    /**
     * Add an item to a user cart.
     */
    public function addItemToUserCart(Cart $cart, array $productData)
    {
        return DB::transaction(function () use ($cart, $productData) {
            $existingItem = $this->findExistingUserCartItem(
                $cart->id,
                $productData['id'],
                $productData['variant_id'] ?? null
            );

            if ($existingItem) {
                $existingItem->quantity += $productData['quantity'];
                $existingItem->save();
            } else {
                $item = new CartItem();
                $item->cart_id = $cart->id;
                $item->product_id = $productData['id'];
                $item->variant_id = $productData['variant_id'] ?? null;
                $item->purchased_price = $productData['price'] ?? 0;
                $item->quantity = $productData['quantity'];

                if (isset($productData['variant_options'])) {
                    $item->variant_options = $productData['variant_options'];
                }

                $item->save();
            }

            return Cart::getWithAllRelations($cart->id);
        });
    }

    /**
     * Add an item to a guest cart.
     */
    public function addItemToGuestCart(GuestCart $guestCart, array $productData)
    {
        return DB::transaction(function () use ($guestCart, $productData) {
            $existingItem = $this->findExistingGuestCartItem(
                $guestCart->id,
                $productData['id'],
                $productData['variant_id'] ?? null
            );
            if ($existingItem) {
                $existingItem->quantity += $productData['quantity'];
                $existingItem->save();
            } else {
                $item = new GuestCartItem();
                $item->guest_cart_id = $guestCart->id;
                $item->product_id = $productData['id'];
                $item->variant_id = $productData['variant_id'] ?? null;
                $item->purchased_price = $productData['price'] ?? 0;
                $item->quantity = $productData['quantity'];

                // Store variant options if available
                if (isset($productData['variant_options'])) {
                    $item->variant_options = $productData['variant_options'];
                }

                $item->save();
            }

            return $this->getGuestCart($guestCart->guest_token);
        });
    }

    /**
     * Find an existing item in a user cart.
     */
    private function findExistingUserCartItem($cartId, $productId, $variantId = null)
    {
        $query = CartItem::where('cart_id', $cartId)
            ->where('product_id', $productId);

        if ($variantId) {
            $query->where('variant_id', $variantId);
        } else {
            $query->whereNull('variant_id');
        }

        return $query->first();
    }

    /**
     * Find an existing item in a guest cart.
     */
    private function findExistingGuestCartItem($guestCartId, $productId, $variantId = null)
    {
        $query = GuestCartItem::where('guest_cart_id', $guestCartId)
            ->where('product_id', $productId);

        if ($variantId) {
            $query->where('variant_id', $variantId);
        } else {
            $query->whereNull('variant_id');
        }

        return $query->first();
    }

    /**
     * Update an item in a user cart.
     */
    public function updateUserCartItem(Cart $cart, $productId, $quantity, $variantId = null)
    {
        return DB::transaction(function () use ($cart, $productId, $quantity, $variantId) {
            $item = $this->findExistingUserCartItem($cart->id, $productId, $variantId);

            if ($item) {
                $item->quantity = $quantity;
                $item->save();
            }

            return Cart::getWithAllRelations($cart->id);
        });
    }

    /**
     * Update an item in a guest cart.
     */
    public function updateGuestCartItem(GuestCart $guestCart, $productId, $quantity, $variantId = null)
    {
        return DB::transaction(function () use ($guestCart, $productId, $quantity, $variantId) {
            $item = $this->findExistingGuestCartItem($guestCart->id, $productId, $variantId);

            if ($item) {
                $item->quantity = $quantity;
                $item->save();
            }

            return $this->getGuestCart($guestCart->guest_token);
        });
    }

    /**
     * Remove an item from a user cart.
     */
    public function removeUserCartItem(Cart $cart, $productId, $variantId = null)
    {
        return DB::transaction(function () use ($cart, $productId, $variantId) {
            $item = $this->findExistingUserCartItem($cart->id, $productId, $variantId);

            if ($item) {
                $item->delete();
                if ($cart->items()->count() === 0) {
                    $cart->removeAllCoupons();
                }
            }

            return Cart::getWithAllRelations($cart->id);
        });
    }

    /**
     * Remove an item from a guest cart.
     */
    public function removeGuestCartItem(GuestCart $guestCart, $productId, $variantId = null)
    {
        return DB::transaction(function () use ($guestCart, $productId, $variantId) {
            $item = $this->findExistingGuestCartItem($guestCart->id, $productId, $variantId);

            if ($item) {
                $item->delete();
                if ($guestCart->items()->count() === 0) {
                    $guestCart->removeAllCoupons();
                }
            }

            return $this->getGuestCart($guestCart->guest_token);
        });
    }

    /**
     * Clear all items from a user cart.
     */
    public function clearUserCart(Cart $cart)
    {
        return DB::transaction(function () use ($cart) {
            $cart->removeAllCoupons();
            CartItem::where('cart_id', $cart->id)->delete();

            return ['success' => true];
        });
    }

    /**
     * Clear all items from a guest cart.
     */
    public function clearGuestCart(GuestCart $guestCart)
    {
        return DB::transaction(function () use ($guestCart) {
            $guestCart->removeAllCoupons();
            GuestCartItem::where('guest_cart_id', $guestCart->id)->delete();

            return ['success' => true];
        });
    }
}