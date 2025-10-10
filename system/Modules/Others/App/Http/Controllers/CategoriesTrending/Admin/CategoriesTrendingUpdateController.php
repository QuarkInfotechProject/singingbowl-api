<?php
namespace Modules\Others\App\Http\Controllers\CategoriesTrending\Admin;

use Illuminate\Http\Request;
use Modules\Others\Service\CategoriesTrending\Admin\CategoriesTrendingUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoriesTrendingUpdateController extends AdminBaseController
{
    function __construct(private CategoriesTrendingUpdateService $categoriesTrendingUpdateService)
    {
    }

    function __invoke(Request $request)
    {
        $this->categoriesTrendingUpdateService->update($request->all());
        return $this->successResponse('Trending category has been updated successfully.');
    }
}