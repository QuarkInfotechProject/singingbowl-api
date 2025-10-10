<?php

namespace Modules\Others\App\Http\Controllers\CategoriesTrending\Admin;

use Illuminate\Http\Request;
use Modules\Others\Service\CategoriesTrending\Admin\CategoriesTrendingCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoriesTrendingCreateController extends AdminBaseController
{
    function __construct(private CategoriesTrendingCreateService $categoriesTrendingCreateService)
    {
    }

    function __invoke(Request $request)
    {
        $this->categoriesTrendingCreateService->create($request->all());
        return $this->successResponse('Trending category has been created successfully.');
    }
}