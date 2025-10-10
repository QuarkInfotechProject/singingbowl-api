<?php

namespace Modules\Brand\App\Http\Controllers\Admin;

use Modules\Brand\App\Http\Requests\BrandUpdateRequest;
use Modules\Brand\Service\Admin\BrandUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class BrandUpdateController extends AdminBaseController
{
    function __construct(private BrandUpdateService $brandUpdateService)
    {
    }

    function __invoke(BrandUpdateRequest $request)
    {
        $this->brandUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('Brand has been updated successfully.');
    }
}
