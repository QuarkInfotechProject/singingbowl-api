<?php

namespace Modules\Cart\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Cart\Service\CartClearService;

class CartClearController extends CartBaseController
{
    public function __construct(private CartClearService $cartClearService)
    {
    }

    public function __invoke(Request $request)
    {
        $cartType = $request->get('cart_type');
        $cartIdentifier = $request->get('cart_identifier');

        $result = $this->cartClearService->clearCart($cartType, $cartIdentifier);

        $message = $cartType === 'user' ? 'All items removed from cart.' : 'Guest cart cleared successfully.';

        return $this->successResponse($message, $result);
    }
}