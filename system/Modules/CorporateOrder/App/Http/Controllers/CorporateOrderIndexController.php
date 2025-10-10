<?php

namespace Modules\CorporateOrder\App\Http\Controllers;

use Modules\CorporateOrder\Service\CorporateOrderIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CorporateOrderIndexController extends AdminBaseController
{
    function __construct(private CorporateOrderIndexService $corporateOrderIndexService)
    {
    }

    function __invoke()
    {
        $corporateOrders = $this->corporateOrderIndexService->index();

        return $this->successResponse('Orders has been fetched successfully.', $corporateOrders);
    }
}
