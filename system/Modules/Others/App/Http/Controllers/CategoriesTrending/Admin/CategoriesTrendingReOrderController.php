<?php

namespace Modules\Others\App\Http\Controllers\CategoriesTrending\Admin;
use Illuminate\Http\Request;
use Modules\Others\Service\CategoriesTrending\Admin\CategoriesTrendingReOrderService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoriesTrendingReOrderController extends AdminBaseController
{
    function __construct(private CategoriesTrendingReOrderService $categoriesTrendingReOrderService)
    {
    }

    function __invoke(Request $request)
    {
        $this->categoriesTrendingReOrderService->reorder($request);
        return $this->successResponse('Trending category has been reordered successfully.');
    }
}