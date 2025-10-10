<?php
namespace Modules\Others\Service\CategoriesTrending\Admin;

use Modules\Others\App\Models\CategoriesTrending;
use Modules\Product\App\Models\Product;

class CategoriesTrendingShowService
{
    public function getById($id)
    {
        $trendingCategory = CategoriesTrending::query()
            ->where('id', $id)
            ->with([
                'category' => function ($query) {
                    $query->select('id', 'name', 'slug')
                          ->with('files');
                }
            ])
            ->select([
                'id',
                'category_id',
                'is_active as isActive',
                'sort_order as sortOrder'
            ])
            ->firstOrFail();

        $category = $trendingCategory->category;
        $categoryId = $trendingCategory->category_id;

        $result = [
            'id'          => $trendingCategory->id,
            'category_id' => $categoryId,
            'isActive'    => $trendingCategory->isActive,
            'sortOrder'   => $trendingCategory->sortOrder,
            'category'    => [
                'id'    => $category->id,
                'name'  => $category->name,
                'slug'  => $category->slug,
                'files' => $this->formatCategoryFiles($category),
            ],
        ];

        $result['products'] = $this->getProductsForCategory($categoryId);

        return $result;
    }
    private function formatCategoryFiles($category)
    {
        if (!$category || $category->files->isEmpty()) {
            return [];
        }
        if ($category->files->first()->type) {
            return $category->files->keyBy('type')
                ->map([$this, 'transformFile'])
                ->toArray();
        }

        $files = $category->files->values();
        $categoryFiles = [];

        if ($files->count() === 1) {
            $categoryFiles['logo'] = $this->transformFile($files->first());
        } elseif ($files->count() >= 2) {
            $categoryFiles['logo'] = $this->transformFile($files[0]);
            $categoryFiles['banner'] = $this->transformFile($files[1]);
        }

        return $categoryFiles;
    }
    private function getProductsForCategory($categoryId)
    {
        return Product::whereHas('categories', function ($query) use ($categoryId) {
                $query->where('product_categories.category_id', $categoryId);
            })
            ->with([
                'reviews' => function ($query) {
                    $query->selectRaw('product_id, COUNT(*) as review_count, AVG(rating) as average_rating')
                          ->groupBy('product_id');
                },
                'files'
            ])
            ->get()
            ->map(function ($product) {
                return [
                    'id'             => $product->id,
                    'uuid'           => $product->uuid,
                    'name'           => $product->product_name,
                    'slug'           => $product->slug,
                    'best_seller'    =>$product->best_seller,
                    'original_price' => $product->original_price,
                    'special_price'  => $product->special_price,
                    'in_stock'       => $product->in_stock,
                    'quantity'       => $product->quantity,
                    'new_from'       => $product->new_from,
                    'new_to'         => $product->new_to,
                    'review_count'   => $product->reviews->first()->review_count ?? 0,
                    'average_rating' => $product->reviews->first()->average_rating ?? 0,
                    'image'          => $this->getImageUrl($product->files),
                ];
            })
            ->toArray();
    }
    private function getImageUrl($files)
    {
        if ($files && $files->isNotEmpty()) {
            $file = $files->first();
            return $file->url;
        }
        return null;
    }
    private function transformFile($file)
    {
        return [
            'id'  => $file->id,
            'url' => $file->url ?? url($file->path) ?? null,
        ];
    }
}
