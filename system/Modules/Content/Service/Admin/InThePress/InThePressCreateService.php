<?php

namespace Modules\Content\Service\Admin\InThePress;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Content\App\Models\InThePress;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class InThePressCreateService
{
    function create($data, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $content = InThePress::create([
                'title' => $data['title'],
                'link' => $data['link'],
                'published_date' => $data['publishedDate']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to create InThePress content: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data,
                'ipAddress' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'In the press content added of title: ' . $data['title'],
                $content->id,
                ActivityTypeConstant::CONTENT_CREATED,
                $ipAddress)
        );
    }
}
