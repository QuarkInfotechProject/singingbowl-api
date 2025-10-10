<?php

namespace Modules\Content\App\Http\Controllers\Admin\NewLaunch;

use Modules\Content\Service\Admin\NewLaunch\NewLaunchContentShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class NewLaunchContentShowController extends AdminBaseController
{
    function __construct(private NewLaunchContentShowService $newLaunchContentShowService)
    {
    }

    function __invoke(int $id)
    {
        $newLaunchContent = $this->newLaunchContentShowService->show($id);

        return $this->successResponse('New launch content has been fetched successfully.', $newLaunchContent);
    }
}
