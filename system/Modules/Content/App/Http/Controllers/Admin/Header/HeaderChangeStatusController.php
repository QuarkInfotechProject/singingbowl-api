<?php

namespace Modules\Content\App\Http\Controllers\Admin\Header;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\Header\HeaderChangeStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class HeaderChangeStatusController extends AdminBaseController
{
    function __construct(private HeaderChangeStatusService $headerChangeStatusService)
    {
    }

    function __invoke(Request $request)
    {
        $this->headerChangeStatusService->changeStatus($request->get('id'));

        return $this->successResponse('Header content status has been changed successfully.');
    }
}
