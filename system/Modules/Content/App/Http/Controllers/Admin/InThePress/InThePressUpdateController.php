<?php

namespace Modules\Content\App\Http\Controllers\Admin\InThePress;

use Modules\Content\App\Http\Requests\InThePress\InThePressUpdateRequest;
use Modules\Content\Service\Admin\InThePress\InThePressUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class InThePressUpdateController extends AdminBaseController
{
    function __construct(private InThePressUpdateService $inThePressUpdateService)
    {
    }

    function __invoke(InThePressUpdateRequest $request)
    {
        $this->inThePressUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('Content has been updated successfully.');
    }
}
