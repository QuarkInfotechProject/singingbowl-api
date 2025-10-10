<?php

namespace Modules\Content\Service\Admin\Content;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\Content;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ContentDestroyService
{
    public function destroy(int $id, string $ipAddress)
    {
        try {
            $content = Content::find($id);

            if (!$content) {
                throw new Exception('Content not found.', ErrorCode::NOT_FOUND);
            }

            Event::dispatch(
                new AdminUserActivityLogEvent(
                    'Content destroyed of type: ' . Content::$contentType[$content->type],
                    $content->id,
                    ActivityTypeConstant::CONTENT_DESTROYED,
                    $ipAddress
                )
            );

            $content->delete();
        } catch (\Exception $exception) {
            Log::error('Failed to destroy content.', [
                'error' => $exception->getMessage(),
                'id' => $id,
                'ip_address' => $ipAddress
            ]);
            throw $exception;
        }
    }

}
