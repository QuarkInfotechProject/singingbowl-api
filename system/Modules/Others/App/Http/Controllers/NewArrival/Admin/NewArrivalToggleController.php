<?php

namespace Modules\Others\App\Http\Controllers\NewArrival\Admin;

use Modules\Others\Service\NewArrival\Admin\NewArrivalToggleService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Illuminate\Http\Request;

class NewArrivalToggleController extends AdminBaseController
{
    function __construct(private NewArrivalToggleService $newArrivalToggleService)
    {
    }

    function __invoke(Request $request, $id)
    {
        $status = $this->newArrivalToggleService->toggle($id);

        return $this->successResponse('Category new arrival status has been updated successfully.', [
            'show_in_new_arrival' => $status
        ]);
    }
}