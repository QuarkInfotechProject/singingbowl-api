<?php

namespace Modules\Attribute\Service\Attribute;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Attribute\App\Models\Attribute;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AttributeDestroyService
{
    function destroy(int $id, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $attribute = Attribute::find($id);

            if (!$attribute) {
                throw new Exception('Attribute not found.', ErrorCode::NOT_FOUND);
            }

            $attribute->delete();

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to delete attribute.', [
                'error' => $exception->getMessage(),
                'attribute_id' => $id,
                'ip_address' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Attribute destroyed of name: ' . $attribute['name'],
                $attribute->id,
                ActivityTypeConstant::ATTRIBUTE_DESTROYED,
                $ipAddress)
        );
    }
}
