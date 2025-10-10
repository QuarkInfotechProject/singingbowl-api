<?php

namespace Modules\Product\App\Http\Controllers\User;

use Modules\Product\Service\User\ProductGetRelatedProductService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductGetRelatedProductController extends UserBaseController
{
    function __construct(private ProductGetRelatedProductService $productGetRelatedProductService)
    {
    }

    function __invoke(string $slug)
    {
        $relatedProducts = $this->productGetRelatedProductService->getRelatedProducts($slug);

        return $this->successResponse('Related products have been fetched successfully.', $relatedProducts);
    }
}
