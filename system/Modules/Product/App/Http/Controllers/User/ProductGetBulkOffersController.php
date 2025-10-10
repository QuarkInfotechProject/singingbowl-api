<?php

namespace Modules\Product\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Product\Service\User\ProductGetBulkOffersService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductGetBulkOffersController extends UserBaseController
{
    public function __construct(private ProductGetBulkOffersService $productGetBulkOffersService)
    {
    }

    public function __invoke()
    {
        $bulkOffers = $this->productGetBulkOffersService->show();

        return $this->successResponse('Product bulk offers has been fetched successfully.', $bulkOffers);
    }
}
