<?php

namespace Modules\Product\App\Http\Controllers\User;

use Modules\Product\Service\User\ProductGetMainSpecsService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductGetMainSpecsController extends UserBaseController
{
    function __construct(private ProductGetMainSpecsService $productGetMainSpecsService)
    {
    }

    function __invoke(string $url)
    {
        $productMainSpecs = $this->productGetMainSpecsService->show($url);

        return $this->successResponse('Product main specifications has been fetched successfully.', $productMainSpecs);
    }
}
