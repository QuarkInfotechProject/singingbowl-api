<?php

namespace Modules\Content\Service\User\InThePress;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Content\App\Models\InThePress;

class InThePressIndexService
{
    function index()
    {
        $results =  InThePress::with([
            'files' => function ($q) {
                $q->whereIn('zone', ['desktopImage', 'mobileImage'])
                    ->select('zone', DB::raw("CONCAT(path, '/', temp_filename) AS \"imageUrl\""));
            },
        ])
            ->select('id', 'title', 'link', 'published_date as publishedDate')
            ->where('is_active', true)
            ->latest()
            ->get();

        $results->transform(function ($result) {
            $result->publishedDate = Carbon::parse($result->publishedDate)->isoFormat('Do MMMM, YYYY');
            return $result;
        });

        return $results;
    }
}
