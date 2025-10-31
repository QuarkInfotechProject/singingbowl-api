<?php

namespace Modules\Product\Service\Admin;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Modules\Product\App\Models\Product;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ProductExclusionService
{
    function excludeCurrentProduct(?string $name, string $uuid)
    {
        $productToExclude = Product::where('uuid', $uuid)->first();

        if (!$productToExclude) {
            throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
        }

        if (isset($name)) {
            $page = 1;
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        }

        $query = Product::query();

        $query->where('id', '!=', $productToExclude->id)
            ->where('status', true);

        if ($name !== null) {
            $query->where('product_name', 'like', '%' . $name . '%');
        }

        $products = $query
            ->with([
                'files' => function ($q) {
                    $q->wherePivot('zone', 'baseImage')
                        ->select(DB::raw("CONCAT(path, '/Thumbnail/', temp_filename) AS \"imageUrl\""));
                },
            ])
            ->select('id', 'uuid', 'product_name as name', 'original_price as originalPrice', 'special_price as specialPrice')
            ->latest()
            ->paginate(10);

        $products->getCollection()->transform(function ($product) {
            return [
                'id' => $product->uuid,
                'name' => $product->name,
                'originalPrice' => $product->originalPrice,
                'specialPrice' => $product->specialPrice,
                'files' => $product->files,
            ];
        });

        return $products;
    }
}
