<?php

namespace Modules\Content\Service\User\Affiliate;

use Illuminate\Support\Facades\DB;
use Modules\Content\App\Models\Affiliate;

class AffiliateIndexService
{
    function index(int $isPartner)
    {
        return Affiliate::with([
            'files' => function ($q) {
                $q->whereIn('zone', ['desktopLogo', 'mobileLogo'])
                    ->select('zone', DB::raw("CONCAT(path, '/', temp_filename) AS \"imageUrl\""));
            },
        ])
            ->select('id', 'title', 'description', 'link')
            ->where('is_partner', $isPartner)
            ->where('is_active', true)
            ->latest()
            ->get();
    }
}
