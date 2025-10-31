<?php

namespace Modules\Others\Service\ActiveOffers;

use Illuminate\Support\Facades\DB;
use Modules\Others\App\Models\ActiveOffer;
use Modules\Others\App\Models\Features;

class ActiveOfferIndexService
{
    function index()
    {
        return ActiveOffer::with([
            'files' => function ($q) {
                $q->where('zone', 'image')
                    ->select('zone', DB::raw("CONCAT(path, '/', temp_filename) AS \"imageUrl\""));
            },
        ])
            ->select('id', 'text', 'is_active as isActive')
            ->latest()
            ->paginate(20);
    }
}
