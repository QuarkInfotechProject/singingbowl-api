<?php

namespace Modules\Order\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Order\Service\Admin\OrderDestroyNoteService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class OrderDestroyNoteController extends AdminBaseController
{
    function __construct(private OrderDestroyNoteService $orderDestroyNoteService)
    {
    }

    function __invoke(Request $request)
    {
        $this->orderDestroyNoteService->destroy($request->all());

        return $this->successResponse('Note has been deleted successfully.');
    }
}
