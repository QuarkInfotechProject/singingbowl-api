<?php

namespace Modules\Content\Service\Admin\Content;

use Illuminate\Support\Facades\DB;
use Modules\Content\App\Models\Content;

class ContentIndexService
{
    function index(int $type = null)
    {
        $query = Content::query();

        if ($type !== null) {
            $query->where('type', $type);
        }

        return $query
            ->with([
                'files' => function ($q) {
                    $q->whereIn('zone', ['desktopImage', 'mobileImage'])
                    ->select('zone', DB::raw("CONCAT(path, '/', temp_filename) AS \"imageUrl\""));
                },
            ])
            ->select('id', 'link', 'is_active as isActive', 'type')
            ->latest()
            ->paginate(20);
    }
}
