<?php

namespace Modules\Product\App\Http\Controllers\User;

use Modules\Product\Service\User\ProductGetVariantDescriptionService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductGetVariantDescriptionController extends UserBaseController
{
    public function __construct(private ProductGetVariantDescriptionService $productGetVariantDescriptionService)
    {
    }

    public function __invoke(string $id)
    {
        $variant = $this->productGetVariantDescriptionService->show($id);

        return $this->successResponse('Product variant has been fetched successfully.', $variant);
    }
}
