<?php

namespace Modules\Product\Service\User;

use Modules\Product\App\Models\Product;
use Modules\Shared\Services\CacheService;

class ProductShowNamesService
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    function index()
    {
        $cacheKey = 'product:names:all';
        $cacheTags = ['products'];
        $ttl = $this->cacheService->getProductIndexTtl(); // Reuse index TTL

        return $this->cacheService->remember($cacheKey, function () {
            return Product::pluck('product_name');
        }, $ttl, $cacheTags);
    }
}
