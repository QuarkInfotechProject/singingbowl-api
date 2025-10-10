<?php

namespace Modules\Content\App\Http\Controllers\Admin\InThePress;

use Modules\Content\App\Http\Requests\InThePress\InThePressCreateRequest;
use Modules\Content\Service\Admin\InThePress\InThePressCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class InThePressCreateController extends AdminBaseController
{
    function __construct(private InThePressCreateService $inThePressCreateService)
    {
    }

    function __invoke(InThePressCreateRequest $request)
    {
        $this->inThePressCreateService->create($request->all(), $request->getClientIp());

        return $this->successResponse('In the press content has been created successfully.');
    }
}
