<?php

namespace Modules\Review\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Review\Service\Admin\ReviewDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ReviewDestroyController extends AdminBaseController
{
    function __construct(private ReviewDestroyService $reviewDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->reviewDestroyService->destroy($request->get('uuid'));

        return $this->successResponse('Review has been deleted successfully.');
    }
}
