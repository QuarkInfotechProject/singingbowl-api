<?php

namespace Modules\Attribute\App\Http\Controllers\Attribute;

use Modules\Attribute\Service\Attribute\AttributeIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\Attribute\App\Http\Requests\Attribute\AttributeIndexRequest;

class AttributeIndexController extends AdminBaseController
{
    function __construct(private AttributeIndexService $attributeIndexService)
    {
    }

    function __invoke(AttributeIndexRequest $request)
    {
        $validated = $request->validated();

        $attributes = $this->attributeIndexService->index(
            $validated['name'] ?? null,
            $validated['category'] ?? null
        );

        return $this->successResponse('Attributes have been fetched successfully.', $attributes);
    }
}
