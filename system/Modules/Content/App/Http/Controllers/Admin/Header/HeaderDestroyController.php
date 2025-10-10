<?php

namespace Modules\Content\App\Http\Controllers\Admin\Header;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\Header\HeaderDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class HeaderDestroyController extends AdminBaseController
{
    function __construct(private HeaderDestroyService $headerDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->headerDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('Header content has been deleted successfully.');
    }
}
