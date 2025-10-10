<?php

namespace Modules\Others\App\Http\Controllers\Features;

use Illuminate\Http\Request;
use Modules\Others\Service\Feature\FeatureCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FeatureCreateController extends AdminBaseController
{
    function __construct(private FeatureCreateService $featureCreateService)
    {
    }

    function __invoke(Request $request)
    {
        $this->featureCreateService->create($request->all());

        return $this->successResponse('Feature description has been created successfully.');
    }
}
