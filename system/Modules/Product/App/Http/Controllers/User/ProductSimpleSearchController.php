<?php

namespace Modules\Product\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Product\Service\User\ProductSimpleSearchService;

class ProductSimpleSearchController
{
    public function __construct(
        private ProductSimpleSearchService $service
    ) {}

    public function search(Request $request): JsonResponse
    {
        $data = $this->service->search($request);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $data = $this->service->suggestions($request);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
