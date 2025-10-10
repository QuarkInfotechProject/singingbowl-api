<?php

namespace Modules\Content\Service\User\FlashOffer;

use Illuminate\Support\Facades\DB;
use Modules\Content\App\Models\FlashOffer;

class FlashOfferIndexService
{
    function index()
    {
        return FlashOffer::with([
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
