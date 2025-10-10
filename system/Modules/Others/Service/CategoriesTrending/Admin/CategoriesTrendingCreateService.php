<?php
namespace Modules\Others\Service\CategoriesTrending\Admin;

use Illuminate\Support\Facades\DB;
use Modules\Category\App\Models\Category;
use Modules\Others\App\Events\CategoriesTrendingCreated;
use Modules\Others\App\Models\CategoriesTrending;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CategoriesTrendingCreateService
{
    function create($data)
    {
        $category = Category::where('id', $data['categoriesId'])->first();

        if (!$category) {
            throw new Exception('Category not found.', ErrorCode::NOT_FOUND);
        }
        $existingTrending = CategoriesTrending::where('category_id', $category->id)->first();

        if ($existingTrending) {
            throw new Exception('Trending category with this Id already exists.', ErrorCode::BAD_REQUEST);
        }

        try {
            DB::beginTransaction();

            // Check if user wants to create active trending category
            $isActive = $data['isActive'] ?? true;

            // If trying to create active trending category, check current active count
            if ($isActive) {
                $activeCount = CategoriesTrending::where('is_active', true)->count();

                // If already 6 active trending categories, mark this one as inactive
                if ($activeCount >= 6) {
                    $isActive = false;
                }
            }

            CategoriesTrending::create([
                'category_id' => $category->id,
                'is_active' => $isActive,
                'sort_order' => 0,
            ]);

            $createdTrending = CategoriesTrending::where('category_id', $category->id)->first();

            DB::commit();

            // Fire event for cache invalidation
            if ($createdTrending) {
                event(new CategoriesTrendingCreated($createdTrending));
            }

        } catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
