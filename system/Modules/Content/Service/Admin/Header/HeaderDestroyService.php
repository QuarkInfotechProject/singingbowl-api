<?php

namespace Modules\Content\Service\Admin\Header;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\Header;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class HeaderDestroyService
{
    function destroy(int $id, string $ipAddress)
    {
        try {
            $content = Header::find($id);

            if (!$content) {
                throw new Exception('Header content not found.', ErrorCode::NOT_FOUND);
            }

            Event::dispatch(
                new AdminUserActivityLogEvent(
                    'Header content destroyed.',
                    $content->id,
                    ActivityTypeConstant::HEADER_CONTENT_DESTROYED,
                    $ipAddress
                )
            );

            $content->delete();

        } catch (\Exception $exception) {
            Log::error('Failed to destroy header content.', [
                'error' => $exception->getMessage(),
                'id' => $id,
                'ip_address' => $ipAddress
            ]);
            throw $exception;
        }
    }
}
