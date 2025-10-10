<?php

namespace Modules\Others\App\Http\Controllers\LimitedTimeDeals\Admin;

use Illuminate\Http\Request;
use Modules\Others\Service\LimitedTimeDeals\Admin\LimitedTimeDealStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class LimitedTimeDealStatusController extends AdminBaseController
{
    public function __construct(private LimitedTimeDealStatusService $limitedTimeDealStatusService)
    {
    }

    public function __invoke(Request $request, $id)
    {
        $data = $this->limitedTimeDealStatusService->toggleStatus($id);
        return $this->successResponse('Limited time deal status changed successfully.', $data);
    }
}