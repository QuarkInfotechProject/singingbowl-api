<?php

namespace Modules\Support\App\Http\Controllers\Admin\GeneralSupport;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Support\Service\Admin\GeneralSupport\GeneralSupportIndexService;

class GeneralSupportIndexController extends AdminBaseController
{
    function __construct(private GeneralSupportIndexService $generalSupportIndexService)
    {
    }

    function __invoke()
    {
        $support = $this->generalSupportIndexService->index();

        return $this->successResponse('General support has been fetched successfully.', $support);
    }
}
