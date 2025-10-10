<?php

namespace Modules\Category\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Category\Service\Admin\CategoryReOrderService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoryReOrderController extends AdminBaseController
{
    function __construct(private CategoryReOrderService $categoryReOrderService)
    {
    }

    function __invoke(Request $request)
    {
        $this->categoryReOrderService->reorder($request);
        return $this->successResponse('Category has been reordered successfully.');
    }
}
