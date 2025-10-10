<?php

namespace Modules\OrderProcessing\App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\OrderProcessing\App\Http\Requests\CreateOrderProcessingRequest;
use Modules\OrderProcessing\Service\CreateOrderArtifactsService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CreateOrderArtifactsController extends AdminBaseController
{
    function __construct(private CreateOrderArtifactsService $orderArtifactsService)
    {
    }

    function __invoke(CreateOrderProcessingRequest $request)
    {
        $this->orderArtifactsService->create($request->all());

        return $this->successResponse('order artifacts created successfully.');
    }
}
