<?php

namespace Modules\Tag\Service;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\Tag\App\Models\Tag;

class TagDestroyService
{
    function destroy($id, string $ipAddress)
    {
        try {
            $tag = Tag::find($id);

            if (!$tag) {
                throw new Exception('Tag not found.', ErrorCode::NOT_FOUND);
            }

            Event::dispatch(new AdminUserActivityLogEvent(
                    'Tag destroyed of name: ' . $tag['name'],
                    $tag->id,
                    ActivityTypeConstant::TAG_DESTROYED,
                    $ipAddress)
            );

            $tag->delete();
        } catch (\Exception $exception) {
            Log::error('Error deleting tag: ' . $exception->getMessage(), [
                'exception' => $exception,
                'id' => $id,
                'ipAddress' => $ipAddress
            ]);
            throw $exception;
        }
    }
}
