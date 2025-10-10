<?php

namespace Modules\Product\App\Http\Controllers\User;

use Modules\Product\Service\User\ProductGetDescriptionVideoService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductGetDescriptionVideoController extends UserBaseController
{
    function __construct(private ProductGetDescriptionVideoService $productGetDescriptionVideoService)
    {
    }

    function __invoke(string $url)
    {
        $productImages = $this->productGetDescriptionVideoService->show($url);

        return $this->successResponse('Product description video has been fetched successfully.', $productImages);
    }
}
