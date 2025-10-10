<?php

namespace Modules\CorporateOrder\App\Http\Controllers;

use Modules\CorporateOrder\App\Http\Requests\CorporateOrderCreateRequest;
use Modules\CorporateOrder\Service\CorporateOrderCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CorporateOrderCreateController extends AdminBaseController
{
    function __construct(private CorporateOrderCreateService $corporateOrderCreateService)
    {
    }

    function __invoke(CorporateOrderCreateRequest $request)
    {
        $this->corporateOrderCreateService->create($request->all());

        return $this->successResponse('Order has been placed successfully.');
    }
}
