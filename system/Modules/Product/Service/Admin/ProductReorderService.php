<?php

namespace Modules\Product\Service\Admin;

use Illuminate\Support\Facades\DB;
use Modules\Product\App\Models\Product;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ProductReorderService
{
    public function reOrder(string $id, int $sortOrder)
    {
        DB::transaction(function () use ($id, $sortOrder) {
            $currentItem = Product::where('uuid', $id)->lockForUpdate()->first();

            if (!$currentItem) {
                throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
            }

            $currentSortOrder = $currentItem->sort_order;

            $currentItem->update(['sort_order' => $sortOrder]);

            $direction = $currentSortOrder < $sortOrder ? 'down' : 'up';

            $range = $direction === 'down'
                ? [$currentSortOrder + 1, $sortOrder]
                : [$sortOrder, $currentSortOrder - 1];

            Product::where('id', '!=', $currentItem->id)
                ->whereBetween('sort_order', $range)
                ->orderBy('sort_order')
                ->{$direction === 'down' ? 'decrement' : 'increment'}('sort_order');
        });
    }
}
