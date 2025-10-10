<?php

namespace Modules\Content\App\Http\Controllers\Admin\NewLaunch;

use Modules\Content\App\Http\Requests\NewLaunch\NewLaunchContentUpdateRequest;
use Modules\Content\Service\Admin\NewLaunch\NewLaunchContentUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class NewLaunchContentUpdateController extends AdminBaseController
{
    function __construct(private NewLaunchContentUpdateService $newLaunchContentUpdateService)
    {
    }

    function __invoke(NewLaunchContentUpdateRequest $request)
    {
       $this->newLaunchContentUpdateService->update($request->all(), $request->getClientIp());

       return $this->successResponse('New launch content has been updated successfully.');
    }
}
