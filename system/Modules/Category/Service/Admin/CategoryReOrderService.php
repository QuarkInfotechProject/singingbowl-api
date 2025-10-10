<?php
namespace Modules\Category\Service\Admin;
use Modules\Category\App\Models\Category;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryReOrderService
{
    public function reorder(Request $request): void
    {
        $id = (int) $request->get('id');
        $sortOrder = (int) $request->get('sortOrder');

        if ($sortOrder < 0) {
            throw new Exception('Sort order cannot be negative.', ErrorCode::BAD_REQUEST);
        }

        $currentItem = Category::find($id);

        if (!$currentItem) {
            throw new Exception('Category not found.', ErrorCode::NOT_FOUND);
        }

        $currentSortOrder = $currentItem->sort_order;
        if ($currentSortOrder === $sortOrder) {
            return;
        }

        DB::beginTransaction();
        try {
            $parentId = $currentItem->parent_id;
            $direction = $currentSortOrder < $sortOrder ? 'down' : 'up';

            $min = min($currentSortOrder, $sortOrder);
            $max = max($currentSortOrder, $sortOrder);

            if ($direction === 'down') {
                Category::where('id', '!=', $id)
                    ->where('parent_id', $parentId)
                    ->where('sort_order', '>', $currentSortOrder)
                    ->where('sort_order', '<=', $sortOrder)
                    ->decrement('sort_order');
            } else {
                Category::where('id', '!=', $id)
                    ->where('parent_id', $parentId)
                    ->where('sort_order', '>=', $sortOrder)
                    ->where('sort_order', '<', $currentSortOrder)
                    ->increment('sort_order');
            }

            $currentItem->sort_order = $sortOrder;
            $currentItem->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e instanceof Exception ? $e : new Exception('Failed to reorder category.', ErrorCode::BAD_REQUEST);
        }
    }
}
