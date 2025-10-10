<?php

namespace Modules\Review\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Review\App\Events\ReviewUpdated;
use Modules\Review\App\Models\Review;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ReviewChangeStatusService
{
    function changeStatus(string $uuid)
    {
        $review = Review::where('uuid', $uuid)->first();

        if (!$review) {
            throw new Exception('Product review not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $review->update(['is_approved' => !$review['is_approved']]);

            DB::commit();
            
            // Fire review updated event for cache invalidation
            ReviewUpdated::dispatch($review);
        } catch (\Exception $exception) {
            Log::error('Failed to change review status.', [
                'error' => $exception->getMessage(),
                'review_id' => $review->id
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
