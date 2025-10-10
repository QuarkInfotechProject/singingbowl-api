<?php

namespace Modules\Warranty\App\Http\Controllers\WarrantyClaim;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Warranty\Service\WarrantyClaim\WarrantyClaimIndexService;

class WarrantyClaimIndexController extends AdminBaseController
{
    function __construct(private WarrantyClaimIndexService $warrantyClaimIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $registrations = $this->warrantyClaimIndexService->index($request->all());

        return $this->successResponse('Warranty claim has been fetched successfully.', $registrations);
    }
}
