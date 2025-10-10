<?php

namespace Modules\Product\App\Http\Controllers\User;

use Modules\Product\Service\User\ProductGetSimilarProductService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductGetSimilarProductController extends UserBaseController
{
    function __construct(private ProductGetSimilarProductService $productGetSimilarProductService)
    {
    }

    function __invoke(string $url)
    {
        $product = $this->productGetSimilarProductService->show($url);

        return $this->successResponse('Product has been fetched successfully.', $product);
    }
}
