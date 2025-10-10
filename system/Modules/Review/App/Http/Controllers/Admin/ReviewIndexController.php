<?php

namespace Modules\Review\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Review\Service\Admin\ReviewIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ReviewIndexController extends AdminBaseController
{
    function __construct(private ReviewIndexService $reviewIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $reviews = $this->reviewIndexService->index($request->all());

        return $this->successResponse('Reviews has been fetched successfully.', $reviews);
    }
}
