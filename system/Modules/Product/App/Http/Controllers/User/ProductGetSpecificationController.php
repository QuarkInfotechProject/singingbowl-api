<?php

namespace Modules\Product\App\Http\Controllers\User;

use Modules\Product\Service\User\ProductGetSpecificationService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductGetSpecificationController extends UserBaseController
{
    function __construct(private ProductGetSpecificationService $productGetSpecificationService)
    {
    }

    function __invoke(string $url)
    {
        $product = $this->productGetSpecificationService->show($url);

        return $this->successResponse('Product specification has been fetched successfully.', $product);
    }
}
