<?php

namespace Modules\Category\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Category\App\Models\Category;
use Modules\Category\DTO\CategoryUpdateDTO;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CategoryUpdateService
{
    function update(CategoryUpdateDTO $categoryUpdateDTO, string $ipAddress)
    {
        $category = Category::find($categoryUpdateDTO->id);

        if (!$category) {
            throw new Exception('Category not found', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $category->update([
                'name' => $categoryUpdateDTO->name,
                'description' => $categoryUpdateDTO->description,
                'slug' => $categoryUpdateDTO->url,
                'is_searchable' => $categoryUpdateDTO->searchable,
                'is_active' => $categoryUpdateDTO->status,
                'is_displayed' => $categoryUpdateDTO->isDisplayed,
                'filter_price_min' => $categoryUpdateDTO->filterPriceMin,
                'filter_price_max' => $categoryUpdateDTO->filterPriceMax,
                ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to update category.', [
                'error' => $exception->getMessage(),
                'category_id' => $categoryUpdateDTO->id,
                'ip_address' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Category updated of name: ' . $category['name'],
                $category->id,
                ActivityTypeConstant::CATEGORY_UPDATED,
                $ipAddress)
        );
    }
}
