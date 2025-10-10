<?php

namespace Modules\Cart\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Cart\App\Http\Requests\CartAddItemRequest;
use Modules\Cart\Service\CartAddItemService;

class CartAddItemController extends CartBaseController
{
    public function __construct(private CartAddItemService $cartAddItemService)
    {
    }

    public function __invoke(CartAddItemRequest $request)
    {
        $cartType = $request->get('cart_type');
        $cartIdentifier = $request->get('cart_identifier');
        $validatedData = $request->validatedWithTransformedKeys();

        $cart = $this->cartAddItemService->addItem(
            $cartType,
            $cartIdentifier,
            $validatedData['products'],
            $request->header('User-Agent')
        );

        $message = $cartType === 'user' ? 'Product added to cart successfully!' : 'Item added to guest cart successfully.';

        return $this->successResponse($message, $cart);
    }
}