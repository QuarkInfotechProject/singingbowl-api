<?php

namespace Modules\Category\App\Http\Controllers\User;

use Modules\Category\Service\User\CategoryIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoryIndexController extends AdminBaseController
{
    function __construct(private CategoryIndexService $categoryIndexService)
    {
    }

    function __invoke()
    {
        $categories = $this->categoryIndexService->index();

        return $this->successResponse('Categories has been fetched successfully.', $categories);
    }
}
