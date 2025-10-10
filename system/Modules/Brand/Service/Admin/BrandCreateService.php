<?php

namespace Modules\Brand\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Brand\App\Models\Brand;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class BrandCreateService
{
    public function create($data, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $brand = Brand::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'status' => $data['status'],

            ]);

            $brand->save();

            DB::commit();

            Event::dispatch(
                new AdminUserActivityLogEvent(
                    'Brand added with name: "' . $brand->name . '"',
                    $brand->id,
                    ActivityTypeConstant::BRAND_CREATED,
                    $ipAddress
                )
            );


            return $brand;

        } catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
