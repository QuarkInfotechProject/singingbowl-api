<?php

namespace Modules\Others\App\Http\Controllers\CategoriesTrending\Admin;

use Illuminate\Http\Request;
use Modules\Others\Service\CategoriesTrending\Admin\CategoriesTrendingStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoriesTrendingStatusController extends AdminBaseController
{
    function __construct(private CategoriesTrendingStatusService $categoriesTrendingStatusService)
    {
    }

    function __invoke(Request $request)
    {
        $this->categoriesTrendingStatusService->updateStatus($request->all());
        return $this->successResponse('Trending category status has been updated successfully.');
    }
}
