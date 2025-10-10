<?php

namespace Modules\Content\App\Http\Controllers\Admin\Content;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\Content\ContentChangeStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ContentChangeStatusController extends AdminBaseController
{
    function __construct(private ContentChangeStatusService $contentChangeStatusService)
    {
    }

    function __invoke(Request $request)
    {
        $this->contentChangeStatusService->changeStatus($request->get('id'));

        return $this->successResponse('Content status has been changed successfully.');
    }
}
