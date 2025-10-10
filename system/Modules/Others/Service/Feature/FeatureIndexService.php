<?php

namespace Modules\Others\Service\Feature;

use Illuminate\Support\Facades\DB;
use Modules\Others\App\Models\Features;

class FeatureIndexService
{
    function index()
    {
        return Features::select('id', 'text', 'is_active as isActive')
            ->latest()
            ->paginate(20);
    }
}
