<?php

namespace Modules\Product\Service\User;

use Modules\Product\App\Models\Product;
use Modules\Product\Trait\GetBasicProductInformationTrait;

class ProductBestSellerService
{
    use GetBasicProductInformationTrait;

    public function index()
    {
        $products = Product::query()
            ->select(
                'id',
                'uuid',
                'product_name',
                'slug',
                'brand_id',
                'best_seller',
                'new_from',
                'new_to',
                'sale_start',
                'sale_end',
                'in_stock',
                'original_price',
                'special_price',
                'special_price_start',
                'special_price_end',
                'has_variant',
                'created_at'
            )
            ->where('status', true)
            ->where('best_seller', true)
            ->latest()
            ->get();

        return $this->getBasicProductInformation($products);
    }
}
