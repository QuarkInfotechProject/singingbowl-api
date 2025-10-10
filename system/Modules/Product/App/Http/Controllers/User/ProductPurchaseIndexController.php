<?php

namespace Modules\Product\App\Http\Controllers\User;

use Modules\Product\Service\User\ProductPurchaseIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductPurchaseIndexController extends UserBaseController
{
    public function __construct(private ProductPurchaseIndexService $productPurchasedService)
    {
    }

    public function __invoke()
    {
        $products = $this->productPurchasedService->index();

        return $this->successResponse('User purchased products has been fetched successfully.', $products);
    }
}
