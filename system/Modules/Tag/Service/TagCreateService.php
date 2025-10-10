<?php

namespace Modules\Tag\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Tag\App\Models\Tag;

class TagCreateService
{
    function create($data, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $tag = Tag::create([
                'name' => $data['name'],
                'slug' => $data['url']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error creating tag: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data,
                'ipAddress' => $ipAddress
            ]);
            DB::rollBack();
            throw  $exception;
        }

        Event::dispatch(new AdminUserActivityLogEvent(
                'Tag created of name: ' . $data['name'],
                $tag->id,
                ActivityTypeConstant::TAG_CREATED,
                $ipAddress)
        );
    }
}
