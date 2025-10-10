<?php

namespace Modules\Others\Service\CategoriesTrending\Admin;

use Illuminate\Http\Request;
use Modules\Others\App\Models\CategoriesTrending;

class CategoriesTrendingIndexService
{
    public function getAll(Request $request)
    {
        $filters = $request->only(['is_active']);

        return CategoriesTrending::query()
            ->when(isset($filters['is_active']), function($query) use ($filters) {
                $query->where('is_active', $filters['is_active']);
            })
            ->orderBy('sort_order', 'asc')
            ->with(['category' => function($query) {
                $query->select('id', 'name', 'slug')
                      ->with('files');
            }])
            ->select([
                'id',
                'category_id',
                'is_active as isActive',
                'sort_order as sortOrder'
            ])
            ->get();
    }
}