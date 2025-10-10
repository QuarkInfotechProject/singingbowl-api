<?php

namespace Modules\Content\Service\User\BestSeller;

use Illuminate\Support\Facades\DB;
use Modules\Content\App\Models\BestSeller;

class BestSellerIndexService
{
    function index()
    {
        return BestSeller::with([
            'files' => function ($q) {
                $q->whereIn('zone', ['desktopFile', 'mobileFile'])
                    ->select('zone', DB::raw("CONCAT(path, '/', temp_filename) AS imageUrl"));
            },
        ])
            ->select('id', 'name', 'link')
            ->where('is_active', true)
            ->latest()
            ->get();
    }
}
