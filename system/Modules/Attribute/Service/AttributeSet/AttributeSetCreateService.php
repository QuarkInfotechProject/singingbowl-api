<?php

namespace Modules\Attribute\Service\AttributeSet;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Attribute\App\Models\AttributeSet;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class AttributeSetCreateService
{
    function create($data, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $attributeSet = AttributeSet::create([
                'name' => $data['name']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to create attribute set.', [
                'error' => $exception->getMessage(),
                'data' => $data,
                'ip_address' => $ipAddress
            ]);
            DB::rollBack();
            throw  $exception;
        }

        Event::dispatch(new AdminUserActivityLogEvent(
                'Attribute set created of name: ' . $data['name'],
                $attributeSet->id,
                ActivityTypeConstant::ATTRIBUTE_SET_CREATED,
                $ipAddress)
        );
    }
}
