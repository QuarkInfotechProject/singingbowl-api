<?php

namespace Modules\Content\App\Http\Controllers\Admin\Affiliate;

use Modules\Content\App\Http\Requests\Affiliate\AffiliateCreateRequest;
use Modules\Content\Service\Admin\Affiliate\AffiliateCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AffiliateCreateController extends AdminBaseController
{
    function __construct(private AffiliateCreateService $affiliateCreateService)
    {
    }

    function __invoke(AffiliateCreateRequest $request)
    {
        $this->affiliateCreateService->create($request->all(), $request->getClientIp());

        return $this->successResponse('Affiliate content has been created successfully.');
    }
}
