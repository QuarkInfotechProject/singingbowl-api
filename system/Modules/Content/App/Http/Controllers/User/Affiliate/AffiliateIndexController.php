<?php

namespace Modules\Content\App\Http\Controllers\User\Affiliate;

use Illuminate\Http\Request;
use Modules\Content\Service\User\Affiliate\AffiliateIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class AffiliateIndexController extends UserBaseController
{
    function __construct(private AffiliateIndexService $affiliateIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $affiliates = $this->affiliateIndexService->index($request->query('isPartner'));

        return $this->successResponse('Affiliate has been fetched successfully.', $affiliates);
    }
}
