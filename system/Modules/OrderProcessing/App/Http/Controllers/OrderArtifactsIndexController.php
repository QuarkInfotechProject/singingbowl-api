<?php

namespace Modules\OrderProcessing\App\Http\Controllers;

use Modules\OrderProcessing\Service\OrderArtifactsIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class OrderArtifactsIndexController extends AdminBaseController
{
    function __construct(private OrderArtifactsIndexService $orderArtifactsIndexService)
    {
    }

    function __invoke()
    {
        $orderArtifacts = $this->orderArtifactsIndexService->index();

        return $this->successResponse('Order Artifacts has been fetched successfully.', $orderArtifacts);
    }
}
