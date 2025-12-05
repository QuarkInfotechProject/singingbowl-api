<?php

namespace Modules\Category\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Category\App\Models\Category;
use Modules\Category\DTO\CategoryCreateDTO;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class CategoryCreateService
{
    function create(CategoryCreateDTO $categoryCreateDTO, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $category = Category::create([
                'name' => $categoryCreateDTO->name,
                'description' => $categoryCreateDTO->description,
                'slug' => $categoryCreateDTO->url,
                'is_searchable' => $categoryCreateDTO->searchable,
                'is_active' => $categoryCreateDTO->status,
                'parent_id' => $categoryCreateDTO->parentId,
                'filter_price_min' => $categoryCreateDTO->filterPriceMin,
                'filter_price_max' => $categoryCreateDTO->filterPriceMax,
            ]);

            DB::commit();
        } catch (\Exception $exception){
            Log::error('Failed to create category.', [
                'error' => $exception->getMessage(),
                'name' => $categoryCreateDTO->name
            ]);
            DB::rollBack();
            throw $exception;
        }

         Event::dispatch(
             new AdminUserActivityLogEvent(
                 'Category added of name: ' . $categoryCreateDTO->name,
                 $category->id,
                 ActivityTypeConstant::CATEGORY_CREATED,
                 $ipAddress)
         );
    }
}
