<?php

namespace Modules\Content\App\Http\Controllers\Admin\Content;

use Modules\Content\Service\Admin\Content\ContentShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ContentShowController extends AdminBaseController
{
    function __construct(private ContentShowService $contentShowService)
    {
    }

    function __invoke(int $id)
    {
        $content = $this->contentShowService->show($id);

        return $this->successResponse('Content has been fetched successfully.', $content);
    }
}
