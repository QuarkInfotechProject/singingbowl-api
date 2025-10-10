<?php

namespace Modules\Product\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Product\Service\User\ProductShowService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductShowController extends UserBaseController
{
    function __construct(private ProductShowService $productShowService)
    {
    }

    function __invoke(Request $request, string $url)
    {
        $product = $this->productShowService->show($request, $url);

        return $this->successResponse('Product has been fetched successfully.', $product);
    }
}
