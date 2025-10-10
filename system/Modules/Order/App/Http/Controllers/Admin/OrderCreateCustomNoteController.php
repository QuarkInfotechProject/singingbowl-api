<?php

namespace Modules\Order\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Order\Service\Admin\OrderCreateCustomNoteService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class OrderCreateCustomNoteController extends AdminBaseController
{
    function __construct(private OrderCreateCustomNoteService $orderCreateCustomNoteService)
    {
    }

    function __invoke(Request $request)
    {
        $this->orderCreateCustomNoteService->create($request->all());

        return $this->successResponse('Note created successfully.');
    }
}
