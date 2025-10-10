<?php
namespace Modules\Others\App\Http\Controllers\CategoriesTrending\User;

use Modules\Others\Service\CategoriesTrending\User\CategoriesTrendingIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class CategoriesTrendingIndexController extends UserBaseController
{
    public function __construct(private CategoriesTrendingIndexService $categoriesTrendingIndexService)
    {
    }

    public function __invoke()
    {
        $trendingCategories = $this->categoriesTrendingIndexService->getAll();
        return $this->successResponse('Trending categories retrieved successfully.', $trendingCategories);
    }
}
