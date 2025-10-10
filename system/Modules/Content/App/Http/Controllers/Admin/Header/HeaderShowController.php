<?php

namespace Modules\Content\App\Http\Controllers\Admin\Header;

use Modules\Content\Service\Admin\Header\HeaderShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class HeaderShowController extends AdminBaseController
{
    function __construct(private HeaderShowService $headerShowService)
    {
    }

    function __invoke(int $id)
    {
        $content = $this->headerShowService->show($id);

        return $this->successResponse('Header content has been fetched successfully.', $content);
    }
}
