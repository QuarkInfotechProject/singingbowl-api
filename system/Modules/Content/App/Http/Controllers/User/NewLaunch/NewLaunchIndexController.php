<?php

namespace Modules\Content\App\Http\Controllers\User\NewLaunch;

use Modules\Content\Service\User\NewLaunch\NewLaunchIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class NewLaunchIndexController extends UserBaseController
{
    function __construct(private NewLaunchIndexService $newLaunchIndexService)
    {
    }

    function __invoke()
    {
        $newLaunches = $this->newLaunchIndexService->index();

        return $this->successResponse('New launch has been fetched successfully.', $newLaunches);
    }
}
