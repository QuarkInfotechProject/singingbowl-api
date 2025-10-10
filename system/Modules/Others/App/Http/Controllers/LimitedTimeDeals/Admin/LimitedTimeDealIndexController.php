<?php

namespace Modules\Others\App\Http\Controllers\LimitedTimeDeals\Admin;

use Illuminate\Http\Request;
use Modules\Others\Service\LimitedTimeDeals\Admin\LimitedTimeDealIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class LimitedTimeDealIndexController extends AdminBaseController
{
    public function __construct(private LimitedTimeDealIndexService $limitedTimeDealIndexService)
    {
    }

    public function __invoke(Request $request)
    {
        $data = $this->limitedTimeDealIndexService->index($request->all());
        return $this->successResponse('Limited time deals retrieved successfully.', $data);
    }
}