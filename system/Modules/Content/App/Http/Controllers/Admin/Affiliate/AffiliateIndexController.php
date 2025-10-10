<?php

namespace Modules\Content\App\Http\Controllers\Admin\Affiliate;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\Affiliate\AffiliateIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AffiliateIndexController extends AdminBaseController
{
    function __construct(private AffiliateIndexService $affiliateIndexService)
    {
    }

    function __invoke(Request $request)
    {
       $affiliates = $this->affiliateIndexService->index($request->get('isPartner'));

       return $this->successResponse('Affiliates has been fetched successfully.', $affiliates);
    }
}
