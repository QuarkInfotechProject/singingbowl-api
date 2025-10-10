<?php

namespace Modules\Warranty\App\Http\Controllers\WarrantyRegistration;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Warranty\Service\WarrantyRegistration\WarrantyRegistrationIndexService;

class WarrantyRegistrationIndexController extends AdminBaseController
{
    function __construct(private WarrantyRegistrationIndexService $warrantyRegistrationIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $registrations = $this->warrantyRegistrationIndexService->index($request->all());

        return $this->successResponse('Warranty registration has been fetched successfully.', $registrations);
    }
}
