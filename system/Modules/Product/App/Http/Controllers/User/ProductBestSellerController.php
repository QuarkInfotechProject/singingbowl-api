<?php

namespace Modules\Product\App\Http\Controllers\User;

use Modules\Product\Service\User\ProductBestSellerService;
use Illuminate\Http\JsonResponse;

class ProductBestSellerController
{
    public function __construct(
        private ProductBestSellerService $service
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
