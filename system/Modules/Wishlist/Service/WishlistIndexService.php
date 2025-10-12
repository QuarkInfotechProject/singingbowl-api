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

        $user->load([
            'wishlist.products' => function ($query) {
                $query->select([
                    'products.id',
                    'products.uuid',
                    'products.product_name',
                    'products.slug',
                    'products.brand_id',
                    'products.best_seller',
                    'products.has_variant',
                    'products.original_price',
                    'products.special_price',
                    'products.special_price_start',
                    'products.special_price_end',
                    'products.in_stock',
                    'products.quantity',
                    'products.new_from',
                    'products.new_to',
                    'products.created_at'
                ]);
            }
        ]);

        $wishlist = $user->wishlist;

        if (!$wishlist) {
            throw new Exception('Wishlist not found.', ErrorCode::NOT_FOUND);
        }

        $products = $wishlist->products->isEmpty() ? [] : $this->getBasicProductInformation($wishlist->products);

        return $products;
    }
}
