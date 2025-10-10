<?php

namespace Modules\Attribute\App\Http\Controllers\CategoryAttribute;

use Modules\Attribute\App\Http\Requests\CategoryAttribute\CategoryAttributeIndexRequest;
use Modules\Attribute\Service\CategoryAttribute\CategoryAttributeIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CategoryAttributeIndexController extends AdminBaseController
{
    function __construct(private CategoryAttributeIndexService $categoryAttributeIndexService)
    {
    }

    function __invoke(CategoryAttributeIndexRequest $request)
    {
        $validated = $request->validated();

        $attributes = $this->categoryAttributeIndexService->index($validated['category_id']);

        return $this->successResponse('Category attributes has been fetched successfully.', $attributes);
    }
}
