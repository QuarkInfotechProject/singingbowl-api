<?php

namespace Modules\Content\Service\User\NewLaunch;

use Illuminate\Support\Facades\DB;
use Modules\Content\App\Models\NewLaunch;

class NewLaunchIndexService
{
    function index()
    {
        return NewLaunch::with([
            'files' => function ($q) {
                $q->whereIn('zone', ['desktopImage', 'mobileImage'])
                    ->select('zone', DB::raw("CONCAT(path, '/', temp_filename) AS \"imageUrl\""));
            },
        ])
            ->select('id', 'link', 'is_banner as isBanner')
            ->where('is_active', true)
            ->latest()
            ->get();
    }
}
