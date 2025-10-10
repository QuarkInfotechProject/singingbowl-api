<?php

namespace Modules\Review\App\Http\Controllers\User\Review;

use Modules\Review\App\Http\Requests\Review\ReviewCreateRequest;
use Modules\Review\Service\User\Review\ReviewCreateService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ReviewCreateController extends UserBaseController
{
    function __construct(private ReviewCreateService $reviewCreateService)
    {
    }

    function __invoke(ReviewCreateRequest $request)
    {
        $this->reviewCreateService->create($request->all(), $request->getClientIp());

        return $this->successResponse('Review has been submitted successfully.');
    }
}
