<?php

namespace Modules\Product\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Product\App\Http\Requests\ProductBestSellerRequest;
use Modules\Product\Service\Admin\ProductBestSellerService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ProductBestSellerController extends AdminBaseController
{
    public function __construct(private ProductBestSellerService $productBestSellerService)
    {
    }

    public function __invoke(ProductBestSellerRequest $request)
    {
        $this->productBestSellerService->toggleTrending($request->all());
        return $this->successResponse('Product BestSeller status has been updated successfully.');
    }
}