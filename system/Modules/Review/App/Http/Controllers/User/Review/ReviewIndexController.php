<?php

namespace Modules\Review\App\Http\Controllers\User\Review;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Review\Service\User\Review\ReviewIndexService;

class ReviewIndexController
{
    public function __construct(
        private ReviewIndexService $service
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 20), 50);
        $reviews = $this->service->index($perPage);

        return response()->json([
            'success' => true,
            'data' => $reviews->items(),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'last_page' => $reviews->lastPage(),
                'has_more_pages' => $reviews->hasMorePages(),
            ]
        ]);
    }
}
