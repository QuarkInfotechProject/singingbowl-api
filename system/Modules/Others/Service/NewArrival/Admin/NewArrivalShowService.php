<?php
namespace Modules\Others\Service\NewArrival\Admin;

use Modules\Category\App\Models\Category;
use Modules\Product\App\Models\Product;
use Carbon\Carbon;

class NewArrivalShowService
{
    /**
     * Get new arrival products by category ID
     *
     * @param int $categoryId
     * @return array
     */
    public function getProductsByCategoryId(int $categoryId): array
    {
        if (!Category::where('id', $categoryId)
            ->where('is_active', true)
            ->where('show_in_new_arrivals', true)
            ->exists()) {
            return [];
        }

        $now = Carbon::now();

        return Product::query()
            ->whereHas('categories', fn($query) => $query->where('categories.id', $categoryId))
            ->where('status', true)
            ->where(fn($query) => $query->where('new_from', '<=', $now)
                ->where(fn($subQuery) => $subQuery->whereNull('new_to')->orWhere('new_to', '>=', $now)))
            ->with('files')
            ->select([
                'id', 'uuid', 'product_name', 'slug', 'original_price',
                'special_price', 'status', 'in_stock', 'quantity', 'sort_order'
            ])
            ->orderBy('sort_order', 'asc')
            ->get()
            ->map(fn($product) => [
                'id' => $product->id,
                'uuid' => $product->uuid,
                'name' => $product->product_name,
                'slug' => $product->slug,
                'original_price' => $product->original_price,
                'special_price' => $product->special_price,
                'in_stock' => $product->in_stock,
                'quantity' => $product->quantity,
                'image' => optional($product->files->first())->url,
            ])
            ->toArray();
    }
}
