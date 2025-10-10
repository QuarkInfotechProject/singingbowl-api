<?php

namespace Modules\Content\App\Http\Controllers\Admin\Content;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\Content\ContentIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ContentIndexController extends AdminBaseController
{
    function __construct(private ContentIndexService $contentIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $contents = $this->contentIndexService->index($request->get('type'));

        return $this->successResponse('Content has been fetched successfully.', $contents);
    }
}
