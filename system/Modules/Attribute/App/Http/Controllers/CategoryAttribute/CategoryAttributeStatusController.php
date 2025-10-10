<?php

namespace Modules\Attribute\App\Http\Controllers\CategoryAttribute;

use Modules\Attribute\App\Http\Requests\CategoryAttribute\CategoryAttributeStatusRequest;
use Modules\Attribute\Service\CategoryAttribute\CategoryAttributeStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoryAttributeStatusController extends AdminBaseController
{
    function __construct(private CategoryAttributeStatusService $categoryAttributeStatusService)
    {
    }

    function __invoke(CategoryAttributeStatusRequest $request)
    {
        $validated = $request->validated();

        $attribute = $this->categoryAttributeStatusService->changeStatus(
            $validated['category_id'],
            $validated['attribute_id'],
            $validated['is_active']
        );

        return $this->successResponse('Category attribute status has been changed successfully.', $attribute);
    }
}
