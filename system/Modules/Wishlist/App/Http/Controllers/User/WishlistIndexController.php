<?php

namespace Modules\Wishlist\App\Http\Controllers\User;

use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\Wishlist\Service\WishlistIndexService;

class WishlistIndexController extends UserBaseController
{
    function __construct(private WishlistIndexService $wishlistIndexService)
    {
    }

    function __invoke()
    {
        $products = $this->wishlistIndexService->index();

        return $this->successResponse('Wishlist has been fetched successfully.', $products);
    }
}
