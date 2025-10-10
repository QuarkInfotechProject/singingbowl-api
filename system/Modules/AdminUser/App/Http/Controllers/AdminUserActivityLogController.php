<?php

namespace Modules\AdminUser\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\AdminUser\Service\AdminUserActivityLogService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AdminUserActivityLogController extends AdminBaseController
{
    public function __construct(private AdminUserActivityLogService $adminUserActivityLogService)
    {
    }

    public function __invoke(Request $request)
    {
        $logs = $this->adminUserActivityLogService->index($request->all());

        return $this->response('Admin activity logs has been fetched successfully.', $logs);
    }
}
