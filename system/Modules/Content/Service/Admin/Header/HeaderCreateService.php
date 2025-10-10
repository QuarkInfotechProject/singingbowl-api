<?php

namespace Modules\Content\Service\Admin\Header;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\Header;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class HeaderCreateService
{
    function create($data, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $header = Header::create([
                'text' => $data['text'],
                'link' => $data['link'],
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to create header.', [
                'error' => $exception->getMessage(),
                'data' => $data
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Header content added',
                $header->id,
                ActivityTypeConstant::HEADER_CONTENT_CREATED,
                $ipAddress)
        );
    }
}
