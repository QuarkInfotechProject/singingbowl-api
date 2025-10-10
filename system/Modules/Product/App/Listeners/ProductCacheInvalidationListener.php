<?php

namespace Modules\Product\App\Listeners;

use Modules\Product\App\Events\ProductCreated;
use Modules\Product\App\Events\ProductDeleted;
use Modules\Product\App\Events\ProductUpdated;
use Modules\Product\App\Jobs\WarmProductCacheJob;
use Modules\Shared\Services\CacheService;

class ProductCacheInvalidationListener
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function handle(ProductCreated|ProductUpdated|ProductDeleted $event): void
    {
        if ($event instanceof ProductCreated) {
            $this->handleProductCreated($event);
        } elseif ($event instanceof ProductUpdated) {
            $this->handleProductUpdated($event);
        } elseif ($event instanceof ProductDeleted) {
            $this->handleProductDeleted($event);
        }
    }

    private function handleProductCreated(ProductCreated $event): void
    {
        // Invalidate product index pages
        $this->cacheService->forgetByTags(['products']);
        // Invalidate general search results
        $this->cacheService->forgetByTags(['searches']);
        // Invalidate trending categories cache as they contain product data
        $this->cacheService->forgetByTags(['trending-categories']);

        // Warm the cache for the new product and potentially first page of index
        WarmProductCacheJob::dispatch($event->product->id)->delay(now()->addSeconds(10));
    }

    private function handleProductUpdated(ProductUpdated $event): void
    {
        $productId = $event->product->id;
        $tagsToForget = [
            'product:' . $productId,
            'products', // For general lists like names, index pages
            'searches', // For all search results
            'trending-categories' // Since product data is included here
        ];

        // Invalidate all relevant tags
        $this->cacheService->forgetByTags($tagsToForget);

        // Re-warm the cache for the updated product
        WarmProductCacheJob::dispatch($productId)->delay(now()->addSeconds(10));
    }

    private function handleProductDeleted(ProductDeleted $event): void
    {
        $productId = $event->productId;
        $tagsToForget = [
            'product:' . $productId,
            'products',
            'searches',
            'trending-categories'
        ];

        $this->cacheService->forgetByTags($tagsToForget);
    }
}
