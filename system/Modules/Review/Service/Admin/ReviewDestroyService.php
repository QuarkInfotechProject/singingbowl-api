<?php

namespace Modules\Review\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Review\App\Events\ReviewDeleted;
use Modules\Review\App\Models\Review;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ReviewDestroyService
{
    function destroy(string $uuid)
    {
        $review = Review::where('uuid', $uuid)->first();

        if (!$review) {
            throw new Exception('Product review not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $productId = $review->product_id; // Store before deletion
            $review->delete();

            DB::commit();
            
            // Fire review deleted event for cache invalidation
            ReviewDeleted::dispatch($productId);
        } catch (\Exception $exception) {
            Log::error('Failed to delete review.', [
                'error' => $exception->getMessage(),
                'review_id' => $review->id
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
