<?php

namespace Modules\Blog\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Blog\App\Models\Post;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class PostCreateService
{
    function create($data, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $post = Post::create([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'read_time' => $data['readTime'],
                'description' => $data['description']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to create post.', [
                'error' => $exception->getMessage(),
                'data' => $data,
                'ip_address' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Post added of title: "' . $post->title . '"',
                $post->id,
                ActivityTypeConstant::POST_CREATED,
                $ipAddress)
        );
    }
}
