<?php

namespace Modules\Attribute\Service\Attribute;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AttributeIndexService
{
    function index($name, $category = null)
    {
        $query = DB::table('attributes');

        if (isset($name)) {
            $query->where('attributes.name', 'like', '%' . $name . '%');
        }

        // Filter by category if category parameter is provided
        if (isset($category)) {
            $query->join('category_attribute', 'attributes.id', '=', 'category_attribute.attribute_id')
                  ->where('category_attribute.category_id', $category);
        }

        $query->join('attribute_sets', 'attributes.attribute_set_id', '=', 'attribute_sets.id')
            ->select(
                'attributes.id',
                'attribute_sets.name as attributeSet',
                'attributes.name',
                'attributes.created_at as createdAt',
            );

        // Make sure we're not duplicating results if filtering by category
        if (isset($category)) {
            $query->distinct('attributes.id');
        }

        $perPage = request()->query('perPage', 25);

        return $query->latest('attributes.created_at')->paginate($perPage);
    }
}
