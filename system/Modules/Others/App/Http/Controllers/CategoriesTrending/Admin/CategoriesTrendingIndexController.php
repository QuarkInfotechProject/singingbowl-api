<?php

namespace Modules\Others\App\Http\Controllers\CategoriesTrending\Admin;

use Illuminate\Http\Request;
use Modules\Others\Service\CategoriesTrending\Admin\CategoriesTrendingIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoriesTrendingIndexController extends AdminBaseController
{
    function __construct(private CategoriesTrendingIndexService $categoriesTrendingIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $trendingCategories = $this->categoriesTrendingIndexService->getAll($request);
        return $this->successResponse('Trending categories retrieved successfully.', $trendingCategories);
    }
}