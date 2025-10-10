<?php
namespace Modules\Others\App\Http\Controllers\CategoriesTrending\Admin;

use Illuminate\Http\Request;
use Modules\Others\Service\CategoriesTrending\Admin\CategoriesTrendingDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoriesTrendingDestroyController extends AdminBaseController
{
    public function __construct(private CategoriesTrendingDestroyService $categoriesTrendingDestroyService)
    {
    }

    public function __invoke(Request $request, int $id)
    {
        $this->categoriesTrendingDestroyService->destroy($id);
        return $this->successResponse('Trending category has been deleted successfully.');
    }
}
