<?php

namespace Modules\Others\App\Listeners;

use Modules\Others\App\Events\CategoriesTrendingCreated;
use Modules\Others\App\Events\CategoriesTrendingDeleted;
use Modules\Others\App\Events\CategoriesTrendingUpdated;
use Modules\Shared\Services\CacheService;

class CategoriesTrendingCacheInvalidationListener
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function handle(CategoriesTrendingCreated|CategoriesTrendingUpdated|CategoriesTrendingDeleted $event): void
    {
        if ($event instanceof CategoriesTrendingCreated) {
            $this->handleCategoriesTrendingCreated($event);
        } elseif ($event instanceof CategoriesTrendingUpdated) {
            $this->handleCategoriesTrendingUpdated($event);
        } elseif ($event instanceof CategoriesTrendingDeleted) {
            $this->handleCategoriesTrendingDeleted($event);
        }
    }

    private function handleCategoriesTrendingCreated(CategoriesTrendingCreated $event): void
    {
        // Invalidate index pages
        $this->cacheService->forgetByTags(['trending-categories']);
        // Invalidate specific category detail if it exists
        $this->cacheService->forgetByTags(['trending-category:' . $event->categoriesTrending->id]);
    }

    private function handleCategoriesTrendingUpdated(CategoriesTrendingUpdated $event): void
    {
        $id = $event->categoriesTrending->id;
        // Invalidate specific trending category cache
        $this->cacheService->forgetByTags(['trending-category:' . $id]);
        // Invalidate index pages as the list might have changed
        $this->cacheService->forgetByTags(['trending-categories']);
    }

    private function handleCategoriesTrendingDeleted(CategoriesTrendingDeleted $event): void
    {
        $id = $event->categoriesTrendingId;
        // Invalidate specific trending category cache
        $this->cacheService->forgetByTags(['trending-category:' . $id]);
        // Invalidate index pages
        $this->cacheService->forgetByTags(['trending-categories']);
    }
}
