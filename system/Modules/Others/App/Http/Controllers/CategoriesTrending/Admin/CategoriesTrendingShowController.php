<?php
namespace Modules\Others\App\Http\Controllers\CategoriesTrending\Admin;

use Modules\Others\Service\CategoriesTrending\Admin\CategoriesTrendingShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoriesTrendingShowController extends AdminBaseController
{
    function __construct(private CategoriesTrendingShowService $categoriesTrendingShowService)
    {
    }

    function __invoke(int $id)
    {
        $trendingCategory = $this->categoriesTrendingShowService->getById($id);
        return $this->successResponse('Trending category retrieved successfully.', $trendingCategory);
    }
}