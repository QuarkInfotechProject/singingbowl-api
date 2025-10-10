<?php

namespace Modules\Others\Service\LimitedTimeDeals\Admin;

use Modules\Others\App\Models\LimitedTimeDeals;

class LimitedTimeDealIndexService
{
    public function index(array $data)
    {
        $query = LimitedTimeDeals::with(['product', 'product.files']);

        $query->orderBy('sort_order', 'asc');
        $deals = $query->get();

        return [
            'data' => $deals->map(function ($deal) {
                $product = $deal->product;
                $primaryFile = $product->files->first();

                return [
                    'id' => $deal->id,
                    'product_uuid' => $product->uuid,
                    'product_name' => $product->product_name,
                    'status' => $deal->status,
                    'image' => $primaryFile ? $primaryFile->url : null,
                ];
            })
        ];
    }
}