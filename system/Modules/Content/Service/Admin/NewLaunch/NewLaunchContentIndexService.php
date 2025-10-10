<?php

namespace Modules\Content\Service\Admin\NewLaunch;

use Illuminate\Support\Facades\DB;
use Modules\Content\App\Models\NewLaunch;

class NewLaunchContentIndexService
{
    function index()
    {
        return NewLaunch::with([
            'files' => function ($q) {
                $q->whereIn('zone', ['desktopImage', 'mobileImage'])
                    ->select('zone', DB::raw("CONCAT(path, '/', temp_filename) AS imageUrl"));
            },
        ])
            ->select('id', 'link', 'is_active as isActive', 'is_banner as isBanner')
            ->latest()
            ->paginate(20);
    }
}
