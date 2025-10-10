<?php

namespace Modules\Content\Service\Admin\Content;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\Content;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class ContentCreateService
{
    function create($data, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $content = Content::create([
                'link' => $data['link'],
                'type' => $data['type'],
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to create content.', [
                'error' => $exception->getMessage(),
                'data' => $data
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Content added of type: ' . Content::$contentType[$data['type']],
                $content->id,
                ActivityTypeConstant::CONTENT_CREATED,
                $ipAddress)
        );
    }
}
