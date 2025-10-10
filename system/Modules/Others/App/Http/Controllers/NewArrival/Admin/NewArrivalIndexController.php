<?php

namespace Modules\Others\App\Http\Controllers\NewArrival\Admin;

use Illuminate\Http\Request;
use Modules\Others\Service\NewArrival\Admin\NewArrivalIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class NewArrivalIndexController extends AdminBaseController
{
    function __construct(private NewArrivalIndexService $newArrivalIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $categories = $this->newArrivalIndexService->getAll($request);
        return $this->successResponse('New arrival categories retrieved successfully.', $categories);
    }
}