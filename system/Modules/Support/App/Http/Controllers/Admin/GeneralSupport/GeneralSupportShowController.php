<?php

namespace Modules\Support\App\Http\Controllers\Admin\GeneralSupport;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Support\Service\Admin\GeneralSupport\GeneralSupportShowService;

class GeneralSupportShowController extends AdminBaseController
{
    function __construct(private GeneralSupportShowService $generalSupportShowService)
    {
    }

    function __invoke(int $id)
    {
        $support = $this->generalSupportShowService->show($id);

        return $this->successResponse('General support has been fetched successfully.', $support);
    }
}
