<?php

namespace Modules\Others\App\Http\Controllers\Features;

use Modules\Others\Service\Feature\FeatureIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FeatureIndexController extends AdminBaseController
{
    function __construct(private FeatureIndexService $featureIndexService)
    {
    }

    function __invoke()
    {
        $features = $this->featureIndexService->index();

        return $this->successResponse('Features has been fetched successfully.', $features);
    }
}
