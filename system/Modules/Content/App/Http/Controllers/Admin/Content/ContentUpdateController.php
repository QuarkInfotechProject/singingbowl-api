<?php

namespace Modules\Content\App\Http\Controllers\Admin\Content;

use Modules\Content\App\Http\Requests\ContentUpdateRequest;
use Modules\Content\Service\Admin\Content\ContentUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ContentUpdateController extends AdminBaseController
{
    function __construct(private ContentUpdateService $contentUpdateService)
    {
    }

    function __invoke(ContentUpdateRequest $request)
    {
        $this->contentUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('Content has been updated successfully.');
    }
}
