<?php

namespace Modules\Product\Service\User;

use Modules\Category\App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Brand\App\Models\Brand;
use Modules\Product\App\Models\Product;

class ProductFilterDataService
{
    function getFiltersForCategory(string|int $categoryIdentifier): array
    {
        $query = Category::query();
        if (is_numeric($categoryIdentifier)) {
            $query->where('id', $categoryIdentifier);
        } else {
            $query->where('slug', $categoryIdentifier);
        }
        $category = $query->where('is_active', true)
            ->where('is_displayed', true)
            ->first();

        if (!$category) {
            throw new ModelNotFoundException("Category not found with identifier: {$categoryIdentifier}");
        }
        $priceHints = [
            'min' => $category->filter_price_min,
            'max' => $category->filter_price_max,
        ];

        $attributes = $category->attributes()
            ->where('is_enabled', true)
            ->wherePivot('is_active', true)
            ->with(['attributeSet:id,name', 'values:id,attribute_id,value'])
            ->orderBy('category_attribute.sort_order', 'asc')
            ->get();

        $groupedFilters = $this->formatAttributes($attributes);

        $relevantBrandIds = Product::query()
            ->where('status', true)
            ->whereHas('categories', function ($q) use ($category) {
                $q->where('categories.id', $category->id);
            })
            ->whereNotNull('brand_id')
            ->distinct()
            ->pluck('brand_id');

        $brands = Brand::query()
            ->whereIn('id', $relevantBrandIds)
            ->where('status', 1)
            ->select('id', 'name', 'slug')
            ->orderBy('name', 'asc')
            ->get();

        return [
            'price_hints' => $priceHints,
            'filter_groups' => $groupedFilters,
            'brands' => $brands,
        ];
    }

    private function formatAttributes(Collection $attributes): Collection
    {
        return $attributes->map(function ($attribute) {
            return [
                'name'   => $attribute->name,
                'values' => $attribute->values
                    ->map(function ($value) {
                        return ['value' => $value->value];
                    })
                    ->sortBy('value')
                    ->values(),
            ];
        });
    }

}
