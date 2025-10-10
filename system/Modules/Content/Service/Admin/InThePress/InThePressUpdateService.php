<?php

namespace Modules\Content\Service\Admin\InThePress;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\InThePress;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class InThePressUpdateService
{
    function update($data, string $ipAddress)
    {
        $content = InThePress::find($data['id']);

        if (!$content) {
            throw new Exception('Content not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $content->update([
                'title' => $data['title'],
                'link' => $data['link'],
                'published_date' => $data['publishedDate']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to update InThePress content: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data,
                'ipAddress' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'In the press content updated.',
                $content->id,
                ActivityTypeConstant::CONTENT_UPDATED,
                $ipAddress)
        );
    }
}
