<?php
namespace Modules\Others\App\Http\Controllers\CategoriesTrending\User;

use Modules\Others\Service\CategoriesTrending\User\CategoriesTrendingShowService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class CategoriesTrendingShowController extends UserBaseController
{
    public function __construct(private CategoriesTrendingShowService $categoriesTrendingShowService)
    {
    }

    public function __invoke(int $id)
    {
        $trendingCategory = $this->categoriesTrendingShowService->getById($id);
        return $this->successResponse('Trending category retrieved successfully.', $trendingCategory);
    }
}
