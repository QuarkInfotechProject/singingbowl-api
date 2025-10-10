<?php

namespace Modules\Menu\Service;

use Modules\Menu\App\Models\Menu;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class MenuReOrderService
{
    function reOrder(int $id, int $sortOrder)
    {
        // Get the current item
        $currentItem = Menu::find($id);

        if (!$currentItem) {
            throw new Exception('Menu not found.', ErrorCode::NOT_FOUND);
        }

        $currentSortOrder = $currentItem->sort_order;
        $parentId = $currentItem->parent_id;

        // Update the sort order of the item being moved
        $currentItem->update(['sort_order' => $sortOrder]);

        // Determine the direction of movement
        $direction = $currentSortOrder < $sortOrder ? 'down' : 'up';

        // Define the range of affected items
        $range = $direction === 'down'
            ? [$currentSortOrder + 1, $sortOrder]
            : [$sortOrder, $currentSortOrder - 1];

        // Adjust sort orders of other items in the same parent
        Menu::where('id', '!=', $id)
            ->where('parent_id', $parentId)
            ->whereBetween('sort_order', $range)
            ->{$direction === 'down' ? 'decrement' : 'increment'}('sort_order');
    }
}
