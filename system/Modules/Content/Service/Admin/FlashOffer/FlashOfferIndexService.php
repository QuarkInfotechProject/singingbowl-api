<?php

namespace Modules\Content\Service\Admin\FlashOffer;

use Illuminate\Support\Facades\DB;
use Modules\Content\App\Models\FlashOffer;

class FlashOfferIndexService
{
    function index()
    {
        $query = FlashOffer::query();

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
