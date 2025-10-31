<?php

namespace Modules\Content\Service\Admin\Affiliate;

use Illuminate\Support\Facades\DB;
use Modules\Content\App\Models\Affiliate;

class AffiliateIndexService
{
    function index(bool $isPartner = null)
    {
        $query = Affiliate::query();

        if (!is_null($isPartner)) {
            $query->where('is_partner', $isPartner);
        }

        $affiliates = $query
            ->with([
                'files' => function ($q) {
                    $q->whereIn('zone', ['desktopLogo', 'mobileLogo'])
                        ->select('zone', DB::raw("CONCAT(path, '/', temp_filename) AS \"imageUrl\""));
                },
            ])
            ->select('id', 'title', 'description', 'is_partner as isPartner', 'is_active as isActive')
            ->latest()
            ->paginate(20);

        $affiliates->getCollection()->transform(function ($affiliate) {
            if ($affiliate->isPartner) {
                $affiliate->makeHidden(['title', 'description']);
            }
            return $affiliate;
        });

        return $affiliates;
    }
}
