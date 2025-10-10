<?php

namespace Modules\Warranty\App\Http\Controllers\WarrantyClaim;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Warranty\Service\WarrantyClaim\WarrantyClaimShowService;

class WarrantyClaimShowController extends AdminBaseController
{
    function __construct(private WarrantyClaimShowService $warrantyClaimShowService)
    {
    }

    function __invoke(int $id)
    {
        $registration = $this->warrantyClaimShowService->show($id);

        return $this->successResponse('Warranty claim has been fetched successfully.', $registration);
    }
}
