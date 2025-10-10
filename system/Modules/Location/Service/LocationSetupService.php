<?php

namespace Modules\Location\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Location\App\Models\City;
use Modules\Location\App\Models\Country;
use Modules\Location\App\Models\Province;
use Modules\Location\App\Models\Zone;

class LocationSetupService
{
    function store($locations)
    {
        try {
            DB::beginTransaction();

            if (!empty($locations['countries'])) {
                Country::insert($locations['countries']);
            }

            if (!empty($locations['provinces'])) {
                Province::insert($locations['provinces']);
            }

            if (!empty($locations['cities'])) {
                City::insert($locations['cities']);
            }

            if (!empty($locations['zones'])) {
                Zone::insert($locations['zones']);
            }

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to store locations during transaction: ' . $exception->getMessage(), [
                'exception' => $exception
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
