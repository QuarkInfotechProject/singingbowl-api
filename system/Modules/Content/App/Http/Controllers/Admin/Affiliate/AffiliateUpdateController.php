<?php

namespace Modules\Content\App\Http\Controllers\Admin\Affiliate;

use Modules\Content\App\Http\Requests\Affiliate\AffiliateUpdateRequest;
use Modules\Content\Service\Admin\Affiliate\AffiliateUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AffiliateUpdateController extends AdminBaseController
{
    function __construct(private AffiliateUpdateService $affiliateUpdateService)
    {
    }

    function __invoke(AffiliateUpdateRequest $request)
    {
        $this->affiliateUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('Affiliate content has been updated successfully.');
    }
}
