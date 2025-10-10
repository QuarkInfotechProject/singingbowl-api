<?php

namespace Modules\Product\App\Http\Controllers\User;

use Modules\Product\Service\User\ProductByCategoryService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductByCategoryController extends UserBaseController
{
    function __construct(private ProductByCategoryService $productByCategoryService)
    {
    }

    function __invoke(string $categoryName)
    {
        $products = $this->productByCategoryService->index($categoryName);

        return $this->successResponse('Products has been fetched successfully.', $products);
    }
}
