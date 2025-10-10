<?php

namespace Modules\Content\App\Http\Controllers\Admin\Content;

use Modules\Content\App\Http\Requests\ContentCreateRequest;
use Modules\Content\Service\Admin\Content\ContentCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ContentCreateController extends AdminBaseController
{
    function __construct(private ContentCreateService $contentCreateService)
    {
    }

    function __invoke(ContentCreateRequest $request)
    {
        $this->contentCreateService->create($request->all(), $request->getClientIp());

        return $this->successResponse('Content has been created successfully.');
    }
}
