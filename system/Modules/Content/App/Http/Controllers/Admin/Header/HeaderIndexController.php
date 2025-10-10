<?php

namespace Modules\Content\App\Http\Controllers\Admin\Header;

use Modules\Content\Service\Admin\Header\HeaderIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class HeaderIndexController extends AdminBaseController
{
    function __construct(private HeaderIndexService $headerIndexService)
    {
    }

    function __invoke()
    {
        $contents = $this->headerIndexService->index();

        return $this->successResponse('Header content has been fetched successfully.', $contents);
    }
}
