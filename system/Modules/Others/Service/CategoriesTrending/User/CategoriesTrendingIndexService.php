<?php
namespace Modules\Others\Service\CategoriesTrending\User;

use Illuminate\Support\Collection;
use Modules\Others\App\Models\CategoriesTrending;
use Modules\Shared\Services\CacheService;

class CategoriesTrendingIndexService
{
    public function __construct(private CacheService $cacheService)
    {
    }

    public function getAll(): Collection
    {
        $cacheKey = $this->cacheService->getTrendingCategoriesIndexKey();
        $tags = ['trending-categories'];

        return $this->cacheService->remember(
            $cacheKey,
            function () {
                return CategoriesTrending::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order', 'asc')
                    ->with(['category' => function($query) {
                        $query->select('id', 'name', 'slug')
                              ->with('files');
                    }])
                    ->select([
                        'id',
                        'category_id',
                        'is_active as isActive',
                        'sort_order as sortOrder'
                    ])
                    ->get();
            },
            $this->cacheService->getTrendingCategoriesIndexTtl(),
            $tags
        );
    }
}
