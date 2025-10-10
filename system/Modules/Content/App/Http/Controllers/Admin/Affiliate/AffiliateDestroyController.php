<?php

namespace Modules\Content\App\Http\Controllers\Admin\Affiliate;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\Affiliate\AffiliateDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AffiliateDestroyController extends AdminBaseController
{
    function __construct(private AffiliateDestroyService $affiliateDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->affiliateDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('Affiliate content has been deleted successfully.');
    }
}
