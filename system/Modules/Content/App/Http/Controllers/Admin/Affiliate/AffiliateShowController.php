<?php

namespace Modules\Content\App\Http\Controllers\Admin\Affiliate;

use Modules\Content\Service\Admin\Affiliate\AffiliateShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class AffiliateShowController extends AdminBaseController
{
    function __construct(private AffiliateShowService $affiliateShowService)
    {
    }

    function __invoke(int $id)
    {
        $affiliate = $this->affiliateShowService->show($id);

        return $this->successResponse('Affiliate content has been fetched successfully.', $affiliate);
    }
}
