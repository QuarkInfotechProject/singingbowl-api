<?php

namespace Modules\Content\Service\Admin\Content;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\Content;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ContentUpdateService
{
    function update($data, string $ipAddress)
    {
        $content = Content::find($data['id']);

        if (!$content) {
            throw new Exception('Content not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $content->update([
                'link' => $data['link']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to update content.', [
                'error' => $exception->getMessage(),
                'data' => $data,
                'ip_address' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Content updated of type: ' . Content::$contentType[$content->type],
                $content->id,
                ActivityTypeConstant::CONTENT_UPDATED,
                $ipAddress)
        );
    }
}
