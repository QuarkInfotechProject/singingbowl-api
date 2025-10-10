<?php

namespace Modules\Review\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Review\App\Models\Review;
use Modules\Review\App\Models\ReviewReply;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ReviewReplyService
{
    function create($data)
    {
        $review = Review::where('uuid', $data['reviewId'])
                    ->first();

        if (!$review) {
            throw new Exception('Review not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            ReviewReply::create([
                'review_id' => $review->id,
                'content' => $data['content']
            ]);

            $review->update([
                'is_replied' => true
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error occurred while creating review reply: '.$exception->getMessage(), [
                'exception' => $exception
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
