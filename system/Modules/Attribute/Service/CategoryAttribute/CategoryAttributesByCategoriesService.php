<?php

namespace Modules\Attribute\Service\CategoryAttribute;

use Illuminate\Support\Facades\DB;

class CategoryAttributesByCategoriesService
{
    /**
     * Get attributes that are common to ALL provided categories.
     * - Only attributes where attributes.is_enabled = true
     * - Sorted by attributes.created_at (latest first)
     * - Includes attribute set info and attribute values
     *
     * @param array<int,int> $categoryIds
     * @param bool $includeValues
     * @return array
     */
    public function getCommonAttributes(array $categoryIds, bool $includeValues = true): array
    {
        if (empty($categoryIds)) {
            return ['attributes' => []];
        }

        $total = count($categoryIds);

        $rows = DB::table('category_attribute as ca')
            ->join('attributes as a', 'ca.attribute_id', '=', 'a.id')
            ->leftJoin('attribute_sets as s', 'a.attribute_set_id', '=', 's.id')
            ->whereIn('ca.category_id', $categoryIds)
            ->where('a.is_enabled', true)
            ->groupBy('a.id', 'a.name', 'a.attribute_set_id', 's.name', 'a.created_at')
            ->havingRaw('COUNT(DISTINCT ca.category_id) = ?', [$total])
            ->select(
                'a.id',
                'a.name',
                'a.attribute_set_id',
                's.name as attribute_set_name',
                'a.created_at'
            )
            ->orderBy('a.created_at', 'desc')
            ->get();

        if (!$includeValues || $rows->isEmpty()) {
            return [
                'attributes' => $rows->map(function ($r) {
                    return [
                        'id' => (int) $r->id,
                        'name' => $r->name,
                        'attribute_set' => [
                            'id' => $r->attribute_set_id ? (int) $r->attribute_set_id : null,
                            'name' => $r->attribute_set_name,
                        ],
                        'created_at' => $r->created_at,
                        'values' => [],
                    ];
                })->values()->all(),
            ];
        }

        $attributeIds = $rows->pluck('id')->all();

        $valuesByAttribute = DB::table('attribute_values')
            ->select('attribute_id', 'value')
            ->whereIn('attribute_id', $attributeIds)
            ->orderBy('value', 'asc')
            ->get()
            ->groupBy('attribute_id');

        $attributes = $rows->map(function ($r) use ($valuesByAttribute) {
            $vals = ($valuesByAttribute->get($r->id) ?? collect())
                ->pluck('value')
                ->map(fn ($v) => ['value' => $v])
                ->values()
                ->all();

            return [
                'id' => (int) $r->id,
                'name' => $r->name,
                'attribute_set' => [
                    'id' => $r->attribute_set_id ? (int) $r->attribute_set_id : null,
                    'name' => $r->attribute_set_name,
                ],
                'values' => $vals,
            ];
        })->values()->all();

        return ['attributes' => $attributes];
    }
}
