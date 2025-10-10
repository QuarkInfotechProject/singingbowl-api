<?php

namespace Modules\Cart\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Cart\App\Models\Cart;
use Modules\Cart\App\Models\CartItem;
use Modules\Cart\App\Models\GuestCart;
use Modules\Cart\App\Models\GuestCartItem;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductVariant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CartUpdateItemService
{
    /**
     * Update item in cart based on cart type and identifier.
     */
    public function updateItem(string $cartType, $cartIdentifier, array $data)
    {
        try {
            $cartId = $data['cartId'];
            $cartItemId = $data['id'];
            $quantity = $data['quantity'];

            // Validate quantity
            if ($quantity < 1) {
                throw new Exception('Quantity must be at least 1.', ErrorCode::BAD_REQUEST);
            }

            if ($cartType === 'user') {
                $result = $this->updateUserCartItem($cartId, $cartItemId, $quantity, $cartIdentifier);
                // Removed cache invalidation for real-time consistency
                return $result;
            } else {
                $result = $this->updateGuestCartItem($cartId, $cartItemId, $quantity);
                // Removed cache invalidation for real-time consistency
                return $result;
            }
        } catch (\Exception $exception) {
            if ($exception instanceof Exception) {
                throw $exception;
            }

            throw new Exception('An error occurred while updating the item in the cart.', ErrorCode::UNPROCESSABLE_CONTENT);
        }
    }

    /**
     * Update an item in user cart.
     */
    private function updateUserCartItem($cartId, $cartItemId, $quantity, $userId)
    {
        // Get current user cart
        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart || $cart->uuid !== $cartId) {
            throw new Exception('Cart not found.', ErrorCode::NOT_FOUND);
        }

        // Find the item directly
        $item = CartItem::where('id', $cartItemId)
                       ->where('cart_id', $cart->id)
                       ->first();

        if (!$item) {
            throw new Exception('Item not found in the cart.', ErrorCode::NOT_FOUND);
        }

        return DB::transaction(function() use ($item, $cart, $quantity) {
            $this->validateStock($item);

            // Update the quantity
            $item->quantity = $quantity;
            $item->save();

            return [
                'success' => true,
                'cart_id' => $cart->uuid
            ];
        });
    }

    /**
     * Update an item in guest cart.
     */
    private function updateGuestCartItem($cartId, $cartItemId, $quantity)
    {
        // Get guest cart
        $cart = GuestCart::where('guest_token', $cartId)->first();

        if (!$cart) {
            throw new Exception('Cart not found.', ErrorCode::NOT_FOUND);
        }

        // Find the item directly
        $item = GuestCartItem::where('id', $cartItemId)
                            ->where('guest_cart_id', $cart->id)
                            ->first();

        if (!$item) {
            throw new Exception('Item not found in the cart.', ErrorCode::NOT_FOUND);
        }

        return DB::transaction(function() use ($item, $cart, $quantity) {
            // Validate stock before updating
            $this->validateStock($item);

            // Update the quantity
            $item->quantity = $quantity;
            $item->save();

            return [
                'success' => true,
                'cart_id' => $cart->guest_token
            ];
        });
    }

    /**
     * Validate that a product is still in stock before updating quantity.
     */
    private function validateStock($item)
    {
        // Load the product to check stock status
        $product = Product::find($item->product_id);

        if (!$product) {
            throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
        }

        // Check if status exists and is not active (typically status=0 means inactive)
        if (isset($product->status) && $product->status === 0) {
            throw new Exception('Product is not available.', ErrorCode::BAD_REQUEST);
        }

        // If item has a variant, check variant stock and skip product stock check
        if ($item->variant_id) {
            $variant = ProductVariant::find($item->variant_id);

            if (!$variant) {
                throw new Exception('Product variant not found.', ErrorCode::NOT_FOUND);
            }

            // Check if variant is out of stock
            if (!$variant->in_stock) {
                throw new Exception('Product variant is out of stock.', ErrorCode::BAD_REQUEST);
            }
        } else {
            // Only check product stock when no variant is present
            if (!$product->in_stock) {
                throw new Exception('Product is out of stock.', ErrorCode::BAD_REQUEST);
            }
        }
    }
}