<?php

namespace Modules\Content\Service\Admin\NewLaunch;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\NewLaunch;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class NewLaunchContentCreateService
{
    function create($data, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $content = NewLaunch::create([
                'link' => $data['link'],
                'is_banner' => $data['isBanner'],
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to create NewLaunch content: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data,
                'ipAddress' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'New launch content added.',
                $content->id,
                ActivityTypeConstant::NEW_LAUNCH_CONTENT_CREATED,
                $ipAddress)
        );
    }
}
