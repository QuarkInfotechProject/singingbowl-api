<?php

namespace Modules\Review\App\Http\Controllers\Admin;

use Modules\Review\App\Http\Requests\Review\ReviewReplyCreateRequest;
use Modules\Review\Service\Admin\ReviewReplyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ReviewReplyController extends AdminBaseController
{
    function __construct(private ReviewReplyService $reviewReplyService)
    {
    }

    function __invoke(ReviewReplyCreateRequest $request)
    {
        $this->reviewReplyService->create($request->all());

        return $this->successResponse('Reply added successfully.');
    }
}
