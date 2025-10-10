<?php

namespace Modules\Product\Http\Controllers\User;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Modules\Product\Service\User\ProductSearchService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ProductSearchController extends UserBaseController
{
    private ProductSearchService $searchService;

    public function __construct(ProductSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'q' => 'nullable|string|max:255',
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1',
                'brand_ids' => 'nullable|array',
                'brand_ids.*' => 'integer|exists:brands,id',
                'category_ids' => 'nullable|array',
                'category_ids.*' => 'integer|exists:categories,id',
                'min_price' => 'nullable|numeric|min:0',
                'max_price' => 'nullable|numeric|min:0',
                'in_stock_only' => 'nullable|boolean',
                'sort_by' => 'nullable|string|in:newest,price_low_high,price_high_low,popular',
            ]);

            $results = $this->searchService->search($request);
            return $this->successResponse($results, 'Products retrieved successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Search failed: ' . $e->getMessage(), [], 500);
        }
    }

    public function suggestions(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'q' => 'required|string|min:1|max:255',
                'limit' => 'nullable|integer|min:1|max:20',
            ]);

            $suggestions = $this->searchService->getSuggestions($request);
            return $this->successResponse($suggestions, 'Suggestions retrieved successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get suggestions: ' . $e->getMessage(), [], 500);
        }
    }

    public function popularSearches(): JsonResponse
    {
        try {
            $popularSearches = $this->searchService->getPopularSearches();
            return $this->successResponse([
                'popular_searches' => $popularSearches
            ], 'Popular searches retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get popular searches: ' . $e->getMessage(), [], 500);
        }
    }
}
