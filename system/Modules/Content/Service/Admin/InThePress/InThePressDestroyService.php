<?php

namespace Modules\Content\Service\Admin\InThePress;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\InThePress;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class InThePressDestroyService
{
    function destroy(int $id, string $ipAddress)
    {
        try {
            $content = InThePress::find($id);

            if (!$content) {
                throw new Exception('Content not found.', ErrorCode::NOT_FOUND);
            }

            Event::dispatch(
                new AdminUserActivityLogEvent(
                    'In the press content destroyed of title: ' . $content->title,
                    $content->id,
                    ActivityTypeConstant::CONTENT_DESTROYED,
                    $ipAddress
                )
            );

            $content->delete();
        } catch (Exception $exception) {
            Log::error('Failed to delete InThePress content: ' . $exception->getMessage(), [
                'exception' => $exception,
                'id' => $id,
                'ipAddress' => $ipAddress
            ]);
            throw $exception;
        }
    }
}
