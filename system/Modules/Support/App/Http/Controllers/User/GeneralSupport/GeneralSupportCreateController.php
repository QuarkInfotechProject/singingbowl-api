<?php

namespace Modules\Support\App\Http\Controllers\User\GeneralSupport;

use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\Support\App\Http\Requests\GeneralSupportCreateRequest;
use Modules\Support\Service\User\GeneralSupport\GeneralSupportCreateService;

class GeneralSupportCreateController extends UserBaseController
{
    function __construct(private GeneralSupportCreateService $generalSupportCreateService)
    {
    }

    function __invoke(GeneralSupportCreateRequest $request)
    {
        $this->generalSupportCreateService->create($request->all());

        return $this->successResponse('General support submitted successfully.');
    }
}
