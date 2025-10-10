<?php

namespace Modules\Cart\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Cart\App\Http\Requests\CartRemoveItemRequest;
use Modules\Cart\Service\CartRemoveItemService;

class CartRemoveItemController extends CartBaseController
{
    public function __construct(private CartRemoveItemService $cartRemoveItemService)
    {
    }

    public function __invoke(CartRemoveItemRequest $request)
    {
        $cartType = $request->get('cart_type');
        $cartIdentifier = $request->get('cart_identifier');
        $validatedData = $request->validatedWithTransformedKeys();

        $result = $this->cartRemoveItemService->removeItem(
            $cartType,
            $cartIdentifier,
            $validatedData
        );

        $message = $cartType === 'user' ? 'Item removed from cart successfully.' : 'Item removed from guest cart successfully.';

        return $this->successResponse($message, $result);
    }
}