<?php

namespace Modules\Attribute\App\Http\Controllers\CategoryAttribute;

use Modules\Attribute\App\Http\Requests\CategoryAttribute\CategoryAttributeSortOrderRequest;
use Modules\Attribute\Service\CategoryAttribute\CategoryAttributeSortOrderService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoryAttributeSortOrderController extends AdminBaseController
{
    function __construct(private CategoryAttributeSortOrderService $categoryAttributeSortOrderService)
    {
    }

    function __invoke(CategoryAttributeSortOrderRequest $request)
    {
        $validated = $request->validated();

        $attributes = $this->categoryAttributeSortOrderService->reOrder(
            $validated['category_id'],
            $validated['attribute_order']
        );

        return $this->successResponse('Category attributes has been reordered successfully.', $attributes);
    }
}
