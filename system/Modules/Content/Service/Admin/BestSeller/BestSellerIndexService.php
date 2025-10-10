<?php

namespace Modules\Content\Service\Admin\BestSeller;

use Illuminate\Support\Facades\DB;
use Modules\Content\App\Models\BestSeller;

class BestSellerIndexService
{
    function index()
    {
        $query = BestSeller::query();

        return $query
            ->with([
                'files' => function ($q) {
                    $q->whereIn('zone', ['desktopFile', 'mobileFile'])
                        ->select('zone', DB::raw("CONCAT(path, '/', temp_filename) AS imageUrl"));
                },
            ])
            ->select('id', 'name', 'link', 'is_active as isActive')
            ->latest()
            ->paginate(20);
    }
}
