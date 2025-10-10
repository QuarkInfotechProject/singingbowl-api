<?php

namespace Modules\Content\App\Http\Controllers\Admin\Content;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\Content\ContentDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ContentDestroyController extends AdminBaseController
{
    function __construct(private ContentDestroyService $contentDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->contentDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('Content has been deleted successfully.');
    }
}
