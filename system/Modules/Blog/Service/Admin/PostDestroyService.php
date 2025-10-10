<?php

namespace Modules\Blog\Service\Admin;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Blog\App\Models\Post;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class PostDestroyService
{
    function destroy(int $id, string $ipAddress)
    {
        try {
            $post = Post::find($id);

            if (!$post) {
                throw new Exception('Post not found.', ErrorCode::NOT_FOUND);
            }

            Event::dispatch(new AdminUserActivityLogEvent(
                'Post destroyed with title: "' . $post->title . '"',
                $post->id,
                ActivityTypeConstant::POST_DESTROYED,
                $ipAddress
            ));

            $post->delete();
        } catch (\Exception $exception) {
            Log::error('Failed to destroy post.', [
                'error' => $exception->getMessage(),
                'post_id' => $id,
                'ip_address' => $ipAddress
            ]);
            throw $exception;
        }
    }
}
