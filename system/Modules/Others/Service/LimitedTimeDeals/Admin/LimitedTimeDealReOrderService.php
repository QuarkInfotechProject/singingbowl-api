<?php
namespace Modules\Others\Service\LimitedTimeDeals\Admin;

use Modules\Others\App\Models\LimitedTimeDeals;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LimitedTimeDealReOrderService
{
    public function reorder(Request $request): void
    {
        $id = (int) $request->get('id');
        $sortOrder = (int) $request->get('sortOrder');

        $currentItem = LimitedTimeDeals::find($id);
        if (!$currentItem) {
            throw new Exception('Limited time deal not found.', ErrorCode::NOT_FOUND);
        }

        $currentSortOrder = $currentItem->sort_order;
        if ($currentSortOrder === $sortOrder) {
            return;
        }

        DB::beginTransaction();
        try {
            $direction = $currentSortOrder < $sortOrder ? 'down' : 'up';
            $range = $direction === 'down'
                ? [$currentSortOrder + 1, $sortOrder]
                : [$sortOrder, $currentSortOrder - 1];

            LimitedTimeDeals::where('id', '!=', $id)
                ->whereBetween('sort_order', $range)
                ->{$direction === 'down' ? 'decrement' : 'increment'}('sort_order');

            $currentItem->sort_order = $sortOrder;
            $currentItem->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e instanceof Exception ? $e : new Exception('Failed to reorder limited time deal.', ErrorCode::BAD_REQUEST);
        }
    }
}