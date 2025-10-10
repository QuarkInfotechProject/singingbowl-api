<?php

namespace Modules\Warranty\App\Http\Controllers\WarrantyRegistration;

use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\Warranty\App\Http\Requests\WarrantyRegistrationCreateRequest;
use Modules\Warranty\Service\WarrantyRegistration\WarrantyRegistrationCreateService;

class WarrantyRegistrationCreateController extends UserBaseController
{
    function __construct(private WarrantyRegistrationCreateService $warrantyRegistrationCreateService)
    {
    }

    function __invoke(WarrantyRegistrationCreateRequest $request)
    {
        $productName = $this->warrantyRegistrationCreateService->create($request->all());

        return $this->successResponse("Warranty registration successful for '$productName'.");
    }
}
