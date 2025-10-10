<?php

namespace Modules\Attribute\Service\AttributeSet;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Attribute\App\Models\AttributeSet;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AttributeSetUpdateService
{
    function update($data, string $ipAddress)
    {
        $attributeSet = AttributeSet::find($data['id']);

        if (!$attributeSet) {
            throw new Exception('Attribute set not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $attributeSet->update([
                'name' => $data['name']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to update attribute set.', [
                'error' => $exception->getMessage(),
                'data' => $data,
                'ip_address' => $ipAddress
            ]);
            DB::rollBack();
            throw  $exception;
        }

        Event::dispatch(new AdminUserActivityLogEvent(
                'Attribute set updated of name: ' . $attributeSet['name'],
                $attributeSet->id,
                ActivityTypeConstant::ATTRIBUTE_SET_UPDATED,
                $ipAddress)
        );
    }
}
