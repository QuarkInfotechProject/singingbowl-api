<?php

namespace Modules\Others\App\Http\Controllers\Features;

use Illuminate\Http\Request;
use Modules\Others\Service\Feature\FeatureDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FeatureDestroyController extends AdminBaseController
{
    function __construct(private FeatureDestroyService $featureDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->featureDestroyService->destroy($request->get('id'));

        return $this->successResponse('Feature has been deleted successfully.');
    }
}
