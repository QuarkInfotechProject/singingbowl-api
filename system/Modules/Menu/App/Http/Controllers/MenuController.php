<?php

namespace Modules\Menu\App\Http\Controllers;

use Modules\Menu\Service\MenuService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class MenuController extends AdminBaseController
{
    function __construct(private MenuService $menuService)
    {
    }

    function __invoke()
    {
        $menus = $this->menuService->index();

        return $this->successResponse('Menu has been fetched successfully.', $menus);
    }
}
