<?php

namespace Modules\Cart\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Cart\App\Http\Requests\CartUpdateItemRequest;
use Modules\Cart\Service\CartUpdateItemService;

class CartUpdateItemController extends CartBaseController
{
    public function __construct(private CartUpdateItemService $cartUpdateItemService)
    {
    }

    public function __invoke(CartUpdateItemRequest $request)
    {
        $cartType = $request->get('cart_type');
        $cartIdentifier = $request->get('cart_identifier');
        $validatedData = $request->validatedWithTransformedKeys();

        $result = $this->cartUpdateItemService->updateItem(
            $cartType,
            $cartIdentifier,
            $validatedData
        );

        $message = $cartType === 'user' ? 'Cart quantity updated successfully.' : 'Guest cart item updated successfully.';

        return $this->successResponse($message, $result);
    }
}