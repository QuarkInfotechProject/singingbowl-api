<?php

namespace Modules\Warranty\App\Http\Controllers\WarrantyRegistration;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Warranty\Service\WarrantyRegistration\WarrantyRegistrationShowService;

class WarrantyRegistrationShowController extends AdminBaseController
{
    function __construct(private WarrantyRegistrationShowService $warrantyRegistrationShowService)
    {
    }

    function __invoke(int $id)
    {
        $registration = $this->warrantyRegistrationShowService->show($id);

        return $this->successResponse('Warranty registration has been fetched successfully.', $registration);
    }
}
