<?php

namespace Modules\Content\App\Http\Controllers\User\Content;

use Illuminate\Http\Request;
use Modules\Content\Service\User\Content\ContentIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ContentIndexController extends UserBaseController
{
    function __construct(private ContentIndexService $contentIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $contents = $this->contentIndexService->index($request->query('type'));

        return $this->successResponse('Content has been fetched successfully.', $contents);
    }
}
