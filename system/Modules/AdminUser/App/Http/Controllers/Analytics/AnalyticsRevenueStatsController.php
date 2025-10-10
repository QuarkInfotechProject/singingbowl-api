<?php

namespace Modules\AdminUser\App\Http\Controllers\Analytics;

use Illuminate\Http\Request;
use Modules\AdminUser\Service\Analytics\AnalyticsRevenueStatsService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AnalyticsRevenueStatsController extends AdminBaseController
{
    function __construct(private AnalyticsRevenueStatsService $analyticsRevenueStatsService)
    {
    }

    function __invoke(Request $request)
    {
        $result = $this->analyticsRevenueStatsService->index($request->all());

        return $this->successResponse('Analytics has been fetched successfully.', $result);
    }
}
