<?php

namespace Modules\Product\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Product\Service\User\ProductSearchService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductSearchController extends UserBaseController
{
    protected ProductSearchService $productSearchService;

    public function __construct(ProductSearchService $productSearchService)
    {
        $this->productSearchService = $productSearchService;
    }

    public function search(Request $request)
    {
        $results = $this->productSearchService->search($request);
        return $this->successResponse($results, 'Products retrieved successfully');
    }

    public function suggestions(Request $request)
    {
        $suggestions = $this->productSearchService->getSuggestions($request);
        return $this->successResponse('Search suggestions fetched successfully.', $suggestions);
    }

    public function popularSearches()
    {
        $popular = $this->productSearchService->getPopularSearches();
        return $this->successResponse(['popular_searches' => $popular], 'Popular searches retrieved successfully');
    }
}
