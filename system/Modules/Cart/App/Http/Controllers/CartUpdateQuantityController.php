<?php

namespace Modules\Cart\App\Http\Controllers;

use Modules\Cart\App\Http\Requests\CartUpdateItemRequest;
use Modules\Cart\Service\CartUpdateItemService;
use Modules\Cart\Service\CartIndexService;

class CartUpdateQuantityController extends CartBaseController
{
    public function __construct(
        private CartUpdateItemService $cartUpdateItemService,
        private CartIndexService $cartIndexService
    ) {
    }

    public function __invoke(CartUpdateItemRequest $request)
    {
        $cartType = $request->get('cart_type');
        $cartIdentifier = $request->get('cart_identifier');
        $validatedData = $request->validatedWithTransformedKeys();

        // Update the cart item quantity
        $this->cartUpdateItemService->updateItem(
            $cartType,
            $cartIdentifier,
            $validatedData
        );

        // Fetch and return the full updated cart data
        $cart = $this->cartIndexService->index($cartType, $cartIdentifier);

        $message = $cartType === 'user' 
            ? 'Cart quantity updated successfully.' 
            : 'Guest cart quantity updated successfully.';

        return $this->successResponse($message, $cart);
    }
}
