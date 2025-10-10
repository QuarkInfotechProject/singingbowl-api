<?php

namespace Modules\Attribute\Service\AttributeSet;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Attribute\App\Models\AttributeSet;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AttributeSetDestroyService
{
    function destroy(int $id, string $ipAddress)
    {
        try {
            $attributeSet = AttributeSet::find($id);

            if (!$attributeSet) {
                throw new Exception('Attribute set not found.', ErrorCode::NOT_FOUND);
            }

            Event::dispatch(new AdminUserActivityLogEvent(
                'Attribute set destroyed with name: ' . $attributeSet->name,
                $attributeSet->id,
                ActivityTypeConstant::ATTRIBUTE_SET_DESTROYED,
                $ipAddress
            ));

            $attributeSet->delete();
        } catch (\Exception $exception) {
            Log::error('Failed to destroy attribute set.', [
                'error' => $exception->getMessage(),
                'attribute_set_id' => $id,
                'ip_address' => $ipAddress
            ]);
            throw $exception;
        }
    }
}
