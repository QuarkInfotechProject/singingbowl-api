<?php
namespace Modules\Category\Service\User;
use Illuminate\Support\Facades\Cache;
use Modules\Category\App\Models\Category;
use Modules\Shared\Services\CacheService;

class CategoryIndexService
{
    private string $cacheKey = 'category_index';
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index()
    {
        // Temporarily commented out cache to resolve tagging issue
        // return $this->cacheService->remember(
        //     $this->cacheKey,
        //     function () {
        //         $categories = Category::select('id', 'name', 'description', 'slug', 'parent_id')
        //             ->where('is_active', true)
        //             ->where('is_displayed', true)
        //             ->with('files') // Eager load files
        //             ->orderBy('sort_order', 'asc')
        //             ->get();
        //         return $this->buildCategoryTree($categories, 0);
        //     },
        //     $this->cacheService->getCategoryIndexTtl(),
        //     ['categories']
        // );

        // Direct query without caching
        $categories = Category::select('id', 'name', 'description', 'slug', 'parent_id')
            ->where('is_active', true)
            ->where('is_displayed', true)
            ->with('files') // Eager load files
            ->orderBy('sort_order', 'asc')
            ->get();
        
        return $this->buildCategoryTree($categories, 0);
    }

    private function buildCategoryTree($categories, $parentId)
    {
        $categoryTree = [];

        foreach ($categories as $category) {
            if ($category->parent_id === $parentId) {
                $file = $category->filterFiles('logo')->first();
                
                $categoryItem = [
                    'id'   => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'slug' => $category->slug,
                    'logo' => $file ? $file->url : null,
                ];

                $children = $this->buildCategoryTree($categories, $category->id);
                if (!empty($children)) {
                    $categoryItem['children'] = $children;
                }

                $categoryTree[] = $categoryItem;
            }
        }

        return $categoryTree;
    }
}
