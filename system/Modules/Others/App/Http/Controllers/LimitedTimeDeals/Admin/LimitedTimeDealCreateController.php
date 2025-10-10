<?php
namespace Modules\Others\App\Http\Controllers\LimitedTimeDeals\Admin;

use Illuminate\Http\Request;
use Modules\Others\Service\LimitedTimeDeals\Admin\LimitedTimeDealCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class LimitedTimeDealCreateController extends AdminBaseController
{
    public function __construct(private LimitedTimeDealCreateService $limitedTimeDealCreateService)
    {
    }

    public function __invoke(Request $request)
    {
        $this->limitedTimeDealCreateService->create($request->all(), $request->getClientIp());
        return $this->successResponse('Limited time deal has been created successfully.');
    }
}