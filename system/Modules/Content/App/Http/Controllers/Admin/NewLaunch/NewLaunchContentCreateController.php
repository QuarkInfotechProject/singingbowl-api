<?php

namespace Modules\Content\App\Http\Controllers\Admin\NewLaunch;

use Modules\Content\App\Http\Requests\NewLaunch\NewLaunchContentCreateRequest;
use Modules\Content\Service\Admin\NewLaunch\NewLaunchContentCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class NewLaunchContentCreateController extends AdminBaseController
{
    function __construct(private NewLaunchContentCreateService $newLaunchContentCreateService)
    {
    }

    function __invoke(NewLaunchContentCreateRequest $request)
    {
        $this->newLaunchContentCreateService->create($request->all(), $request->getClientIp());

        return $this->successResponse('New launch content has been created successfully.');
    }
}
