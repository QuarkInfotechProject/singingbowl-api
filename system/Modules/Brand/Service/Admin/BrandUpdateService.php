<?php

namespace Modules\Brand\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Brand\App\Models\Brand;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class BrandUpdateService
{
    public function update($data, string $ipAddress)
    {
        try {
            $brand = Brand::find($data['id']);

            if (!$brand) {
                throw new Exception('Brand not found.', ErrorCode::NOT_FOUND);
            }

            DB::beginTransaction();

            $brand->update([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'status' => $data['status']


            ]);

            DB::commit();

            Event::dispatch(
                new AdminUserActivityLogEvent(
                    'Brand updated with name: "' . $brand->name . '"',
                    $brand->id,
                    ActivityTypeConstant::BRAND_UPDATED,
                    $ipAddress
                )
            );

        } catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
