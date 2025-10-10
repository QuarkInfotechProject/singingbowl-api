<?php

namespace Modules\CorporateOrder\App\Http\Controllers;

use Modules\CorporateOrder\Service\CorporateOrderShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CorporateOrderShowController extends AdminBaseController
{
    function __construct(private CorporateOrderShowService $corporateOrderShowService)
    {
    }

    function __invoke(int $id)
    {
        $corporateOrder = $this->corporateOrderShowService->show($id);

        return $this->successResponse('Corporate order has been fetched successfully.', $corporateOrder);
    }
}
