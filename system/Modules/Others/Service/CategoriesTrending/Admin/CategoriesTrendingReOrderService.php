<?php

namespace Modules\Others\Service\CategoriesTrending\Admin;

use Modules\Others\App\Events\CategoriesTrendingUpdated;
use Modules\Others\App\Models\CategoriesTrending;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CategoriesTrendingReOrderService
{
    public function reorder(Request $request): void
    {
        $id = (int) $request->get('id');
        $sortOrder = (int) $request->get('sortOrder');

        $currentItem = CategoriesTrending::find($id);

        if (!$currentItem) {
            throw new Exception('Trending category not found.', ErrorCode::NOT_FOUND);
        }

        $currentSortOrder = $currentItem->sort_order;
        if ($currentSortOrder === $sortOrder) {
            return;
        }

        DB::transaction(function () use ($id, $currentItem, $currentSortOrder, $sortOrder) {
            $isMovingDown = $currentSortOrder < $sortOrder;
            $range = $isMovingDown
                ? [$currentSortOrder + 1, $sortOrder]
                : [$sortOrder, $currentSortOrder - 1];

            CategoriesTrending::where('id', '!=', $id)
                ->whereBetween('sort_order', $range)
                ->{$isMovingDown ? 'decrement' : 'increment'}('sort_order');

            $currentItem->sort_order = $sortOrder;
            $currentItem->save();
            
            // Fire event for cache invalidation
            event(new CategoriesTrendingUpdated($currentItem->fresh()));
        }, 3);
    }
}