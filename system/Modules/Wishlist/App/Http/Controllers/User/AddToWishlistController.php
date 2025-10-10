<?php

namespace Modules\Wishlist\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\Wishlist\Service\AddToWishlistService;

class AddToWishlistController extends UserBaseController
{
    function __construct(private AddToWishlistService $addToWishlistService)
    {
    }

    function __invoke(Request $request)
    {
        $this->addToWishlistService->addToWishlist($request->get('productId'), $request->header('User-Agent'));

        return $this->successResponse('Product has been added to wishlist successfully.');
    }
}
