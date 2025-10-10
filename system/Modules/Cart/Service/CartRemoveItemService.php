<?php

namespace Modules\Cart\Service;

use Illuminate\Support\Facades\DB;
use Modules\Cart\App\Models\Cart;
use Modules\Cart\App\Models\CartItem;
use Modules\Cart\App\Models\GuestCart;
use Modules\Cart\App\Models\GuestCartItem;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CartRemoveItemService
{
    /**
     * Remove an item from cart based on cart type and identifier.
     */
    public function removeItem(string $cartType, $cartIdentifier, array $data)
    {
        try {
            $cartId = $data['cartId'];
            $cartItemId = $data['id'];

            if ($cartType === 'user') {
                $result = $this->removeFromUserCart($cartId, $cartItemId, $cartIdentifier);
                // Removed cache invalidation for real-time consistency
                return $result;
            } else {
                $result = $this->removeFromGuestCart($cartId, $cartItemId);
                // Removed cache invalidation for real-time consistency
                return $result;
            }
        } catch (\Exception $exception) {

            if ($exception instanceof Exception) {
                throw $exception;
            }

            throw new Exception('An error occurred while removing the item from the cart.', ErrorCode::UNPROCESSABLE_CONTENT);
        }
    }

    /**
     * Remove an item from user cart.
     */
    private function removeFromUserCart($cartId, $cartItemId, $userId)
    {
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart || $cart->uuid !== $cartId) {
            throw new Exception('Cart not found.', ErrorCode::NOT_FOUND);
        }

        $item = CartItem::where('id', $cartItemId)
                       ->where('cart_id', $cart->id)
                       ->first();

        if (!$item) {
            throw new Exception('Item not found in the cart.', ErrorCode::NOT_FOUND);
        }

        return DB::transaction(function() use ($item, $cart) {
            $item->delete();

            // Reload the cart relationship to get the current item count
            $cart->load('items');

            if ($cart->items->isEmpty()) {
                $cart->removeAllCoupons();
            }

            return [
                'success' => true,
                'cart_id' => $cart->uuid
            ];
        });
    }

    /**
     * Remove an item from guest cart.
     */
    private function removeFromGuestCart($cartId, $cartItemId)
    {
        $cart = GuestCart::where('guest_token', $cartId)->first();

        if (!$cart) {
            throw new Exception('Cart not found.', ErrorCode::NOT_FOUND);
        }

        $item = GuestCartItem::where('id', $cartItemId)
                            ->where('guest_cart_id', $cart->id)
                            ->first();

        if (!$item) {
            throw new Exception('Item not found in the cart.', ErrorCode::NOT_FOUND);
        }

        return DB::transaction(function() use ($item, $cart) {
            $item->delete();

            // Reload the cart relationship to get the current item count
            $cart->load('items');

            if ($cart->items->isEmpty()) {
                $cart->removeAllCoupons();
            }

            return [
                'success' => true,
                'cart_id' => $cart->guest_token
            ];
        });
    }
}