<?php

namespace Modules\Blog\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Blog\App\Models\Post;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class PostChangeStatusService
{
    function changeStatus(int $id)
    {
        $post = Post::find($id);

        if (!$post) {
            throw new Exception('Post not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $post->update(['is_active' => !$post['is_active']]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to change post status.', [
                'error' => $exception->getMessage(),
                'post_id' => $id
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
