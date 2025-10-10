<?php

namespace Modules\Product\App\Http\Controllers\User;

use Modules\Product\Service\User\ProductFilterDataService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductFilterDataController extends UserBaseController
{
    function __construct(private ProductFilterDataService $productFilterDataService)
    {
    }

    function __invoke(string|int $categoryIdentifier)
    {
        $filterData = $this->productFilterDataService->getFiltersForCategory($categoryIdentifier);

        return $this->successResponse('Filter data has been fetched successfully.', $filterData);
    }
}
