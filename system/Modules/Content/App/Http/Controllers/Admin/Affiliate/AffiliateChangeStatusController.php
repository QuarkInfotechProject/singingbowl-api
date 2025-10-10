<?php

namespace Modules\Content\App\Http\Controllers\Admin\Affiliate;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\Affiliate\AffiliateChangeStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AffiliateChangeStatusController extends AdminBaseController
{
    function __construct(private AffiliateChangeStatusService $affiliateChangeStatusService)
    {
    }

    function __invoke(Request $request)
    {
        $this->affiliateChangeStatusService->changeStatus($request->get('id'));

        return $this->successResponse('Affiliate content status has been changed successfully.');
    }
}
