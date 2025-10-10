<?php

namespace Modules\Product\Service\Admin;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Modules\Product\App\Models\Product;
use Modules\Product\DTO\ProductFilterDTO;

class ProductIndexService
{
    function index(ProductFilterDTO $productFilterDTO)
    {
        if (isset($productFilterDTO->name) || isset($productFilterDTO->sku) || isset($productFilterDTO->sortBy)) {
        }

        $query = Product::query();

        $query->when(isset($productFilterDTO->status), function ($query) use ($productFilterDTO) {
            return $query->where('status', $productFilterDTO->status);
        });

        $query->when(isset($productFilterDTO->name), function ($query) use ($productFilterDTO) {
            return $query->where('product_name', 'like', '%' . $productFilterDTO->name . '%');
        });

        if (isset($productFilterDTO->sku)) {
            $query->where('sku', $productFilterDTO->sku);
        }

        $query->select([
            'products.id',
            'products.uuid',
            'brand_id',
            'products.product_name as name',
            'products.sort_order',
            'products.sku',
            'products.original_price',
            'products.special_price',
            'products.special_price_start',
            'products.special_price_end',
            'products.has_variant',
            'products.in_stock',
            'products.status',
            'products.quantity',
            DB::raw('(SELECT COALESCE(MIN(original_price), products.original_price)
                  FROM product_variants
                  WHERE product_variants.product_id = products.id) as sort_price')
        ]);

        if (!empty($productFilterDTO->sortBy)) {
            $sortBy = $productFilterDTO->sortBy;
            $sortDirection = $productFilterDTO->sortDirection ?? 'asc';

            switch ($sortBy) {
                case 'name':
                    $query->orderBy('product_name', $sortDirection);
                    break;
                case 'price':
                    $query->orderBy('sort_price', $sortDirection);
                    break;
                case 'date':
                    $query->orderBy('created_at', $sortDirection);
                    break;
                default:
                    $query->orderBy('sort_order');
                    break;
            }
        } else {
            $query->orderBy('sort_order');
        }

        $perPage = $productFilterDTO->per_page ?? 20;
        $products = $query
            ->with([
                'files' => function ($q) {
                    $q->wherePivot('zone', 'baseImage')
                        ->select(DB::raw("CONCAT(path, '/Thumbnail/', temp_filename) AS imageUrl"));
                },
                'categories',
                'brand'
            ])
            ->withCount(['variants as variantCount'])
            ->paginate($perPage);

        $productItems = [];
        foreach ($products as $product) {
            $productVariant = null;
            $baseImage = null;

            if ($product->has_variant) {
                $productVariant = $this->getFirstProductVariant($product);

                if ($productVariant && $productVariant->optionValues->isNotEmpty()) {
                    $baseImage = $this->getBaseImageFromVariant($productVariant);
                    $productVariant->special_price = $this->validateSpecialPrice($productVariant);
                }
            } else {
                $product->special_price = $this->validateSpecialPrice($product);
            }

            $productItems[] = [
                'id' => $product->id,
                'name' => $product->name,
                'brandId'=>$product->brand_id,
                'hasVariant' => $product->has_variant,
                'category' => $product->categories->pluck('name'),
                'sortOrder' => $product->sort_order,
                'originalPrice' => $product->has_variant && $productVariant ? $productVariant->original_price : $product->original_price,
                'specialPrice' => $product->has_variant && $productVariant ? $productVariant->special_price : $product->special_price,
                'status' => $product->status,
                'inStock' => $product->has_variant && $productVariant ? $productVariant->in_stock : $product->in_stock,
                'quantity' => $product->has_variant && $productVariant ? $productVariant->quantity : $product->quantity,
                'variantCount' => $product->variantCount,
                'files' => $product->files->isNotEmpty() ? $product->files : ($product->has_variant ? $baseImage : null),
            ];
        }

        return [
            'data' => $productItems,
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $perPage,
            'total' => $products->total()
        ];
    }

    private function getFirstProductVariant($product)
    {
        $productOptionId = $product->options()->where('name', 'Color')->value('id');

        return $product->variants()->with('optionValues.files')
            ->whereHas('optionValues', function ($query) use ($productOptionId) {
                $query->where('product_option_id', $productOptionId);
            })->first(['id', 'original_price', 'special_price', 'special_price_start', 'special_price_end', 'in_stock', 'quantity']);
    }

    private function getBaseImageFromVariant($variant)
    {
        $file = $variant->optionValues->first()->filterFiles('baseImage')->first();
        if ($file) {
            return $file->path . '/Thumbnail/' . $file->temp_filename;
        } else {
             return null;
        }
    }

    private function validateSpecialPrice($product)
    {
        $now = Carbon::now();

        if (
            $product->special_price > 0 &&
            (!$product->special_price_start || $now->gte($product->special_price_start)) &&
            (!$product->special_price_end || $now->lte($product->special_price_end))
        ) {
            return $product->special_price;
        }

        return '';
    }
}
