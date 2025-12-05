<?php

namespace Modules\Product\App\Http\Controllers\User;

use Modules\Product\Service\User\ProductListByCategoryService;
use Illuminate\Http\JsonResponse;

class ProductListByCategoryController
{
    public function __construct(
        private ProductListByCategoryService $service
    ) {}

    public function __invoke(): JsonResponse
    {
        $data = $this->service->index();
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
