<?php

namespace Modules\Attribute\Service\Attribute;

use Illuminate\Support\Facades\DB;
use Modules\Attribute\App\Models\Attribute;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AttributeShowService
{
    function show(int $id)
    {
        $attributeData = DB::table('attributes')
            ->select(
                'attributes.id',
                'attributes.name',
                'attributes.is_enabled',
                'attributes.sort_order',
                'attribute_sets.id as attributeSetId'
            )->join('attribute_sets', 'attributes.attribute_set_id', '=', 'attribute_sets.id')
            ->where('attributes.id', $id)
            ->first();

        if (!$attributeData) {
            throw new Exception('Attribute not found.', ErrorCode::NOT_FOUND);
        }

        $attributeValues = DB::table('attribute_values')
            ->select('id', 'value')
            ->where('attribute_id', $id)
            ->get();

        $categoryIds = DB::table('category_attribute')
            ->where('attribute_id', $id)
            ->pluck('category_id')
            ->toArray();

        return array_merge(
            (array) $attributeData,
            [
                'values' => $attributeValues->toArray(),
                'category_ids' => $categoryIds,
            ]
        );
    }
}
