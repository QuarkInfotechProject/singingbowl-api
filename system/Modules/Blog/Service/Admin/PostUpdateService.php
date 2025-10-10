<?php

namespace Modules\Blog\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Blog\App\Models\Post;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class PostUpdateService
{
    function update($data, string $ipAddress)
    {
        $post = Post::find($data['id']);

        if (!$post) {
            throw new Exception('Post not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $post->update([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'read_time' => $data['readTime'],
                'description' => $data['description']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to update post.', [
                'error' => $exception->getMessage(),
                'data' => $data,
                'ip_address' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Post updated of title: "' . $post->title . '"',
                $post->id,
                ActivityTypeConstant::POST_UPDATED,
                $ipAddress
            )
        );
    }
}
