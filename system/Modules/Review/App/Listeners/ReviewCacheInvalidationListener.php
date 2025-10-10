<?php

namespace Modules\Review\App\Listeners;

use Modules\Review\App\Events\ReviewCreated;
use Modules\Review\App\Events\ReviewDeleted;
use Modules\Review\App\Events\ReviewUpdated;
use Modules\Shared\Services\CacheService;

class ReviewCacheInvalidationListener
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function handle(ReviewCreated|ReviewUpdated|ReviewDeleted $event): void
    {
        if ($event instanceof ReviewCreated) {
            $this->handleReviewCreated($event);
        } elseif ($event instanceof ReviewUpdated) {
            $this->handleReviewUpdated($event);
        } elseif ($event instanceof ReviewDeleted) {
            $this->handleReviewDeleted($event);
        }
    }

    private function handleReviewCreated(ReviewCreated $event): void
    {
        $productId = $event->review->product_id;
        $this->invalidateProductCaches($productId);
    }

    private function handleReviewUpdated(ReviewUpdated $event): void
    {
        $productId = $event->review->product_id;
        $this->invalidateProductCaches($productId);
    }

    private function handleReviewDeleted(ReviewDeleted $event): void
    {
        $productId = $event->productId;
        $this->invalidateProductCaches($productId);
    }

    private function invalidateProductCaches(int $productId): void
    {
        // Invalidate the specific product's cache (including related products)
        $this->cacheService->forgetByTags(['product:' . $productId]);

        // Also invalidate any other product lists that might include review data
        $this->cacheService->forgetByTags(['products']);
        $this->cacheService->forgetByTags(['searches']);
        $this->cacheService->forgetByTags(['trending-categories']);
    }
}
