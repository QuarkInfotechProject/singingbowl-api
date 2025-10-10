<?php

namespace Modules\Product\App\Http\Controllers\User;

use Modules\Product\Service\User\ProductShowNamesService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductShowNamesController extends UserBaseController
{
    function __construct(private ProductShowNamesService $productShowNamesService)
    {
    }

    function __invoke()
    {
        $products = $this->productShowNamesService->index();

        return $this->successResponse('Product has been fetched successfully.', $products);
    }
}
