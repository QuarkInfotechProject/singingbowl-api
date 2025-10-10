<?php

namespace Modules\Content\App\Http\Controllers\Admin\NewLaunch;

use Modules\Content\Service\Admin\NewLaunch\NewLaunchContentIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class NewLaunchContentIndexController extends AdminBaseController
{
    function __construct(private NewLaunchContentIndexService $newLaunchContentIndexService)
    {
    }

    function __invoke()
    {
        $newLaunchContents = $this->newLaunchContentIndexService->index();

        return $this->successResponse('New launch content has been fetched successfully.', $newLaunchContents);
    }
}
