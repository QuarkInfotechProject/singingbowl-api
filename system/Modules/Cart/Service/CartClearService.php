<?php

namespace Modules\Cart\Service;

use Modules\Cart\Service\CartRepository;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CartClearService
{
    public function __construct(private CartRepository $cartRepository)
    {
    }

    /**
     * Clear all items from cart based on cart type and identifier.
     */
    public function clearCart(string $cartType, $cartIdentifier)
    {
        // Get cart based on type and clear all items
        if ($cartType === 'user') {
            $result = $this->clearUserCart($cartIdentifier);
            // Removed cache invalidation for real-time consistency
            return [
                'success' => true
            ];
        } else {
            $result = $this->clearGuestCart($cartIdentifier);
            // Removed cache invalidation for real-time consistency
            return [
                'success' => true
            ];
        }
    }

    /**
     * Clear all items from the user cart.
     */
    private function clearUserCart($userId)
    {
        $cart = $this->cartRepository->getCart('user', $userId);

        if (!$cart) {
            throw new Exception('Cart not found.', ErrorCode::NOT_FOUND);
        }

        return $this->cartRepository->clearUserCart($cart);
    }

    /**
     * Clear all items from the guest cart.
     */
    private function clearGuestCart($guestToken)
    {
        $guestCart = $this->cartRepository->getCart('guest', $guestToken);

        if (!$guestCart) {
            throw new Exception('Guest cart not found.', ErrorCode::NOT_FOUND);
        }

        return $this->cartRepository->clearGuestCart($guestCart);
    }
}