<?php

namespace Modules\Order\App\Http\Controllers\User;

use Modules\Order\App\Http\Requests\OrderCreateRequest;
use Modules\Order\Service\User\OrderCreateService;
use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\Cart\App\Models\Cart;
// use Modules\Cart\App\Models\CartItem; // No longer directly used here
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Keep for now, may be used by OrderCreateService or other parts

class OrderCreateController extends UserBaseController
{
    function __construct(private OrderCreateService $orderCreateService)
    {
    }

    function __invoke(OrderCreateRequest $request)
    {
        $user = Auth::user(); // Order creation must be by an authenticated user
        // $userId = $user->id; // $user is guaranteed by auth:user middleware for this route group
        // $sessionId = session()->getId(); // Not relevant for cart retrieval anymore

        Log::info('OrderCreateController: Starting checkout process', [
            'user_id' => $user->id,
            // 'session_id' => $sessionId // Optional for logging if still desired
        ]);

        $cart = Cart::getForCurrentUser(); // This now only gets the cart for the authenticated user

        if (!$cart) {
            Log::warning('No cart found for authenticated user during checkout.', [
                'user_id' => $user->id,
            ]);
            throw new Exception('Your shopping cart is not found. Please try adding items again.', ErrorCode::NOT_FOUND);
        }

        // Eager load items if not already loaded by getForCurrentUser (though it should be simple)
        // Or ensure your getForCurrentUser loads items if that's desired for this check.
        // A simple Cart::getForCurrentUser()->first() might not load items by default.
        // Let's assume Cart::getForCurrentUser() as refactored primarily gets the cart, items check is separate.
        // For safety, explicitly load items here if not guaranteed by getForCurrentUser()
        $cart->loadMissing('items');

        if ($cart->items->isEmpty()) {
            Log::warning('Cart is empty for authenticated user during checkout.', [
                'user_id' => $user->id,
                'cart_id' => $cart->id,
                'cart_uuid' => $cart->uuid
            ]);
            throw new Exception('Your shopping cart is empty. Please add items to your cart before checking out.', ErrorCode::NOT_FOUND);
        }

        Log::info('Cart found for checkout', [
            'user_id' => $user->id,
            'cart_id' => $cart->id,
            'cart_uuid' => $cart->uuid,
            'item_count' => $cart->items->count()
        ]);

        $response = $this->orderCreateService->create($request);

        return $this->successResponse('Order has been placed successfully.', $response);
    }
}
