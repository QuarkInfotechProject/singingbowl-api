<?php
namespace Modules\Others\App\Http\Controllers\LimitedTimeDeals\Admin;

use Illuminate\Http\Request;
use Modules\Others\Service\LimitedTimeDeals\Admin\LimitedTimeDealReOrderService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class LimitedTimeDealReOrderController extends AdminBaseController
{
    function __construct(private LimitedTimeDealReOrderService $limitedTimeDealReOrderService)
    {
    }

    function __invoke(Request $request)
    {
        $this->limitedTimeDealReOrderService->reorder($request);
        return $this->successResponse('Limited time deal has been reordered successfully.');
    }
}