<?php

namespace Modules\Product\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Product\Service\User\ProductIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductIndexController extends UserBaseController
{
    function __construct(private ProductIndexService $productIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $products = $this->productIndexService->index($request);

        return $this->successResponse('Products has been fetched successfully.', $products);
    }
}
