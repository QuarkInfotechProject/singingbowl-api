<?php

namespace Modules\Wishlist\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\Wishlist\Service\RemoveFromWishlistService;

class RemoveFromWishlistController extends UserBaseController
{
    function __construct(private RemoveFromWishlistService $removeFromWishlistService)
    {
    }

    function __invoke(Request $request)
    {
        $this->removeFromWishlistService->removeFromWishlist($request->get('productId'), $request->header('User-Agent'));

        return $this->successResponse('Product has been removed from wishlist successfully.');
    }
}
