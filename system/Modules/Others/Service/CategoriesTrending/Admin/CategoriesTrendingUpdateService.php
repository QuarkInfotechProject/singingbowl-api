<?php
namespace Modules\Others\Service\CategoriesTrending\Admin;

use Illuminate\Support\Facades\DB;
use Modules\Category\App\Models\Category;
use Modules\Others\App\Events\CategoriesTrendingUpdated;
use Modules\Others\App\Models\CategoriesTrending;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CategoriesTrendingUpdateService
{
    function update($data)
    {
        if (!isset($data['id'])) {
            throw new Exception('Trending category ID is required.', ErrorCode::BAD_REQUEST);
        }

        $trendingCategory = CategoriesTrending::find($data['id']);
        if (!$trendingCategory) {
            throw new Exception('Trending category not found.', ErrorCode::NOT_FOUND);
        }

        if (isset($data['categoriesId'])) {
            $category = Category::where('id', $data['categoriesId'])->first();
            if (!$category) {
                throw new Exception('Category not found.', ErrorCode::NOT_FOUND);
            }

            if ($trendingCategory->category_id != $data['categoriesId']) {
                $existingTrending = CategoriesTrending::where('category_id', $data['categoriesId'])->first();
                if ($existingTrending) {
                    throw new Exception('Trending category with this Id already exists.', ErrorCode::BAD_REQUEST);
                }
            }
        }

        try {
            DB::beginTransaction();

            $updateData = [];
            if (isset($data['categoriesId'])) {
                $updateData['category_id'] = $data['categoriesId'];
            }
            if (isset($data['isActive'])) {
                // Check if user wants to activate trending category
                $isActive = $data['isActive'];

                // If trying to activate and currently inactive
                if ($isActive && !$trendingCategory->is_active) {
                    $activeCount = CategoriesTrending::where('is_active', true)->count();

                    // If already 6 active trending categories, mark this one as inactive
                    if ($activeCount >= 6) {
                        $isActive = false;
                    }
                }

                $updateData['is_active'] = $isActive;
            }
            if (isset($data['sortOrder'])) {
                $updateData['sort_order'] = $data['sortOrder'];
            }

            $trendingCategory->update($updateData);

            DB::commit();
            
            // Fire event for cache invalidation
            event(new CategoriesTrendingUpdated($trendingCategory->fresh()));
            
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}