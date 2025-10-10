<?php

namespace Modules\Warranty\App\Http\Controllers\WarrantyClaim;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Warranty\App\Http\Requests\WarrantyClaimCreateRequest;
use Modules\Warranty\Service\WarrantyClaim\WarrantyClaimCreateService;

class WarrantyClaimCreateController extends AdminBaseController
{
    function __construct(private WarrantyClaimCreateService $warrantyClaimCreateService)
    {
    }

    function __invoke(WarrantyClaimCreateRequest $request)
    {
        $product = $this->warrantyClaimCreateService->create($request->all());

        return $this->successResponse("Warranty claim successful for '$product'.");
    }
}
