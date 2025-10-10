<?php

namespace Modules\Others\App\Http\Controllers\Features;

use Modules\Others\Service\Feature\FeatureShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FeatureShowController extends AdminBaseController
{
    function __construct(private FeatureShowService $featureShowService)
    {
    }

    function __invoke(int $id)
    {
        $feature = $this->featureShowService->show($id);

        return $this->successResponse('Features has been fetched successfully.', $feature);
    }
}
