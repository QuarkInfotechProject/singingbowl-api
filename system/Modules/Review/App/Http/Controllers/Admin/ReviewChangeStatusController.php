<?php

namespace Modules\Review\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Review\Service\Admin\ReviewChangeStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ReviewChangeStatusController extends AdminBaseController
{
    public function __construct(private ReviewChangeStatusService $reviewChangeStatusService)
    {
    }

    public function __invoke(Request $request)
    {
        $this->reviewChangeStatusService->changeStatus($request->get('uuid'));

        return $this->successResponse('Product review has been updated successfully.');
    }
}
