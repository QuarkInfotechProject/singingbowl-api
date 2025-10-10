<?php

namespace Modules\Location\Service;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class LocationIndexService
{
    function index()
    {
        $filePath = module_path('Location', 'Config/locations.json');

        if (!File::exists($filePath)) {
            Log::error('Locations file not found at path: ' . $filePath);
            throw new Exception('Location file not found', ErrorCode::NOT_FOUND);
        }

        return json_decode(File::get($filePath), true) ?? [];

//        $locations = Country::select('id', 'name', 'code')
//            ->with([
//                'provinces:id,name,country_id',
//                'provinces.cities:id,name,province_id',
//                'provinces.cities.zones:id,name,city_id'
//            ])
//            ->get();
//
//        $locations = $locations->map(function($country) {
//            return [
//                'id' => $country->id,
//                'name' => $country->name,
//                'code' => $country->code,
//                'provinces' => $country->provinces->map(function($province) {
//                    return [
//                        'id' => $province->id,
//                        'name' => $province->name,
//                        'countryId' => $province->country_id,
//                        'cities' => $province->cities->map(function($city) {
//                            return [
//                                'id' => $city->id,
//                                'name' => $city->name,
//                                'provinceId' => $city->province_id,
//                                'zones' => $city->zones->map(function($zone) {
//                                    return [
//                                        'id' => $zone->id,
//                                        'name' => $zone->name,
//                                        'cityId' => $zone->city_id,
//                                    ];
//                                }),
//                            ];
//                        }),
//                    ];
//                }),
//            ];
//        });
//
//        return [
//            'locations' => $locations
//        ];
    }
}
