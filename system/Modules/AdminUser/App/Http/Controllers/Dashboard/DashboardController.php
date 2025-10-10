<?php

namespace Modules\AdminUser\App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Modules\AdminUser\Service\Dashboard\DashboardService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class DashboardController extends AdminBaseController
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    public function __invoke(Request $request)
    {
        $data = $this->dashboardService->index($request);

        return $this->successResponse('Dashboard has been fetched successfully.', $data);
    }
}
