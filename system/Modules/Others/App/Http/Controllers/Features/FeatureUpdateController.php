<?php

namespace Modules\Others\App\Http\Controllers\Features;

use Illuminate\Http\Request;
use Modules\Others\Service\Feature\FeatureUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FeatureUpdateController extends AdminBaseController
{
    function __construct(private FeatureUpdateService $featureUpdateService)
    {
    }

    function __invoke(Request $request)
    {
        $this->featureUpdateService->update($request->all());

        return $this->successResponse('Feature has been updated successfully.');
    }
}
