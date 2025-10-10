<?php

namespace Modules\Product\App\Http\Controllers\User;

use Modules\Product\Service\User\ProductIndexWithVideoService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductIndexWithVideoController extends UserBaseController
{
    function __construct(private ProductIndexWithVideoService $productIndexWithVideoService)
    {
    }

    function __invoke()
    {
        $products = $this->productIndexWithVideoService->index();

        return $this->successResponse('Products has been fetched successfully.', $products);
    }
}
