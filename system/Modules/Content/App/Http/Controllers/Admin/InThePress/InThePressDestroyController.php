<?php

namespace Modules\Content\App\Http\Controllers\Admin\InThePress;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\InThePress\InThePressDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class InThePressDestroyController extends AdminBaseController
{
    function __construct(private InThePressDestroyService $inThePressDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->inThePressDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('Content has been deleted successfully.');
    }
}
