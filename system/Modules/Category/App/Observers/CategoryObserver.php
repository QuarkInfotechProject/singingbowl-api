<?php

namespace Modules\Category\App\Observers;

use Modules\Category\App\Models\Category;
use Modules\Shared\Services\CacheService;

class CategoryObserver
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function saved(Category $category): void
    {
        $this->cacheService->forgetByTags(['trending-categories', 'categories']);
    }

    public function deleted(Category $category): void
    {
        $this->cacheService->forgetByTags(['trending-categories', 'categories']);
    }
}