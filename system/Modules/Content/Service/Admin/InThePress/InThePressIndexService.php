<?php

namespace Modules\Content\Service\Admin\InThePress;

use Carbon\Carbon;
use Modules\Content\App\Models\InThePress;

class InThePressIndexService
{
    function index()
    {
        $paginatedResults = InThePress::select('id', 'title', 'is_active as isActive', 'published_date as publishedDate')
            ->latest()
            ->paginate(20);

        $paginatedResults->getCollection()->transform(function ($item) {
            $item->publishedDate = Carbon::parse($item->publishedDate)->isoFormat('Do MMMM, YYYY');
            return $item;
        });

        return $paginatedResults;
    }
}
