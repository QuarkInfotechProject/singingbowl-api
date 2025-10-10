<?php

namespace Modules\Review\App\Http\Controllers\Admin;

use Modules\Review\Service\Admin\ReviewShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ReviewShowController extends AdminBaseController
{
    function __construct(private ReviewShowService $reviewShowService)
    {
    }

    function __invoke(string $uuid)
    {
        $review = $this->reviewShowService->show($uuid);

        return $this->successResponse('Product review has been fetched successfully.', $review);
    }
}
