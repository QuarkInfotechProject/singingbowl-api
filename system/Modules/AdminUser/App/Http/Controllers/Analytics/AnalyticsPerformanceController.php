<?php

namespace Modules\AdminUser\App\Http\Controllers\Analytics;

use Illuminate\Http\Request;
use Modules\AdminUser\Service\Analytics\AnalyticsPerformanceService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AnalyticsPerformanceController extends AdminBaseController
{
    function __construct(private AnalyticsPerformanceService $analyticsService)
    {
    }

    function __invoke(Request $request)
    {
        $result = $this->analyticsService->index($request->all());

        return $this->successResponse('Analytics has been fetched successfully.', $result);
    }
}
