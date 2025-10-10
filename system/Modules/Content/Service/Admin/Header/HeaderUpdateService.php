<?php

namespace Modules\Content\Service\Admin\Header;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\Header;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class HeaderUpdateService
{
    function update($data, string $ipAddress)
    {
        $content = Header::find($data['id']);

        if (!$content) {
            throw new Exception('Header content not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $content->update([
                'text' => $data['text'],
                'link' => $data['link'],
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to update header.', [
                'error' => $exception->getMessage(),
                'data' => $data,
                'ip_address' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Header content updated.',
                $content->id,
                ActivityTypeConstant::HEADER_CONTENT_UPDATED,
                $ipAddress)
        );
    }
}
