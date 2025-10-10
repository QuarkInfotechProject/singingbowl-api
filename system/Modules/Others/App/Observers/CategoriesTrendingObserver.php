<?php

namespace Modules\Others\App\Observers;

use Modules\Others\App\Models\CategoriesTrending;
use Modules\Shared\Services\CacheService;

class CategoriesTrendingObserver
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function saved(CategoriesTrending $categoriesTrending): void
    {
        $tagsToForget = [
            'trending-categories',
            'trending-category:' . $categoriesTrending->id
        ];

        $this->cacheService->forgetByTags($tagsToForget);
    }

    public function deleted(CategoriesTrending $categoriesTrending): void
    {
        $tagsToForget = [
            'trending-categories',
            'trending-category:' . $categoriesTrending->id
        ];

        $this->cacheService->forgetByTags($tagsToForget);
    }
}
