<?php

namespace Modules\Content\Service\Admin\NewLaunch;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\NewLaunch;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class NewLaunchContentDestroyService
{
    function destroy(int $id, string $ipAddress)
    {
        $newLaunchContent = NewLaunch::find($id);

        if (!$newLaunchContent) {
            Log::error('Failed to destroy NewLaunch content: Content not found.', [
                'id' => $id,
                'ipAddress' => $ipAddress
            ]);
            throw new Exception('New launch content not found.', ErrorCode::NOT_FOUND);
        }

        try {
            Event::dispatch(
                new AdminUserActivityLogEvent(
                    'New launch content destroyed.',
                    $newLaunchContent->id,
                    ActivityTypeConstant::NEW_LAUNCH_CONTENT_DESTROYED,
                    $ipAddress
                )
            );

            $newLaunchContent->delete();
        } catch (\Exception $exception) {
            Log::error('Failed to destroy NewLaunch content: ' . $exception->getMessage(), [
                'exception' => $exception,
                'id' => $id,
                'ipAddress' => $ipAddress
            ]);
            throw $exception;
        }
    }
}
