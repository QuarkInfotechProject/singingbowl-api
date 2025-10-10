<?php

namespace Modules\Product\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Product\Service\User\ProductShopService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductShopController extends UserBaseController
{
    function __construct(private ProductShopService $productShopService)
    {
    }

    function __invoke(Request $request)
    {
        $products = $this->productShopService->index($request);

        return $this->successResponse('Products has been fetched successfully.', $products);
    }
}
