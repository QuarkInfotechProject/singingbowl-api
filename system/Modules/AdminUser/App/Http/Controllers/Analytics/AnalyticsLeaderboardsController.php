<?php

namespace Modules\AdminUser\App\Http\Controllers\Analytics;

use Illuminate\Http\Request;
use Modules\AdminUser\Service\Analytics\AnalyticsLeaderboardsService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AnalyticsLeaderboardsController extends AdminBaseController
{
    function __construct(private AnalyticsLeaderboardsService $analyticsLeaderboardsService)
    {
    }

    function __invoke(Request $request)
    {
        $result = $this->analyticsLeaderboardsService->index($request->all());

        return $this->successResponse('Analytics has been fetched successfully.', $result);
    }
}
