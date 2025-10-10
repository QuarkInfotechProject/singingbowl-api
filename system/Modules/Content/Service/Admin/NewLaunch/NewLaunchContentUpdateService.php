<?php

namespace Modules\Content\Service\Admin\NewLaunch;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\NewLaunch;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class NewLaunchContentUpdateService
{
    function update($data, string $ipAddress)
    {
        $newLaunchContent = NewLaunch::find($data['id']);

        if (!$newLaunchContent) {
            throw new Exception('New launch content not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $newLaunchContent->update([
                'link' => $data['link'],
                'is_banner' => $data['isBanner']
            ]);

            DB::commit();
        } catch (\Exception $exception){
            Log::error('Failed to update NewLaunch content during transaction: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data,
                'ipAddress' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'New launch content updated.',
                $newLaunchContent->id,
                ActivityTypeConstant::NEW_LAUNCH_CONTENT_UPDATED,
                $ipAddress)
        );
    }
}
