<?php

namespace Modules\Wishlist\Service;

use Illuminate\Support\Facades\Auth;
use Modules\Product\Trait\GetBasicProductInformationTrait;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class WishlistIndexService
{
    use GetBasicProductInformationTrait;

    function index()
    {
        $user = Auth::user();
        $wishlist = $user->wishlist;

        if (!$wishlist) {
            throw new Exception('Wishlist not found.', ErrorCode::NOT_FOUND);
        }

        $products = $wishlist->products->isEmpty() ? [] : $this->getBasicProductInformation($wishlist->products);

        return $products;
    }
}
