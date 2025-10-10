<?php

namespace Modules\Tag\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\Tag\App\Models\Tag;

class TagUpdateService
{
    function update($data, string $ipAddress)
    {
        $tag = Tag::find($data['id']);

        if (!$tag) {
            throw new Exception('Tag not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $tag->update([
                'name' => $data['name'],
                'slug' => $data['url']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error updating tag: ' . $exception->getMessage(), [
                'exception' => $exception,
                'data' => $data,
                'ipAddress' => $ipAddress
            ]);
            DB::rollBack();
            throw  $exception;
        }

        Event::dispatch(new AdminUserActivityLogEvent(
                'Tag updated of name: ' . $tag['name'],
                $tag->id,
                ActivityTypeConstant::TAG_UPDATED,
                $ipAddress)
        );
    }
}
