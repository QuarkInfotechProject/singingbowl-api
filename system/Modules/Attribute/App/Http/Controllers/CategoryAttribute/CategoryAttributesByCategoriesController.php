<?php

namespace Modules\Attribute\App\Http\Controllers\CategoryAttribute;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Attribute\Service\CategoryAttribute\CategoryAttributesByCategoriesService;
use Modules\Attribute\App\Http\Requests\CategoryAttribute\CategoryAttributesByCategoriesRequest;

class CategoryAttributesByCategoriesController extends AdminBaseController
{
    public function __construct(private CategoryAttributesByCategoriesService $service)
    {
    }

    public function __invoke(CategoryAttributesByCategoriesRequest $request)
    {
        $validated = $request->validated();

        $includeValues = (bool)($validated['include_values'] ?? true);

        $data = $this->service->getCommonAttributes($validated['category_ids'], $includeValues);

        return $this->successResponse('Common attributes fetched successfully.', $data);
    }
}
