<?php

namespace Modules\Cart\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Cart\Service\CartIndexService;

class CartIndexController extends CartBaseController
{
    public function __construct(private CartIndexService $cartIndexService)
    {
    }

    public function __invoke(Request $request)
    {
        $cartType = $request->get('cart_type');
        $cartIdentifier = $request->get('cart_identifier');

        $cart = $this->cartIndexService->index($cartType, $cartIdentifier);

        $message = $cartType === 'user' ? 'Cart retrieved successfully.' : 'Guest cart retrieved successfully.';

        return $this->successResponse($message, $cart);
    }
}