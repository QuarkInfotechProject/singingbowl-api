<?php

namespace Modules\Product\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Product\Service\User\ProductShowService; // Corrected namespace
use Modules\Product\Service\User\ProductIndexService; // Corrected namespace
use Illuminate\Http\Request; // Added for creating a dummy request for ProductIndexService
use Illuminate\Support\Facades\Log;

class WarmProductCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int|string $productId;

    public function __construct(int|string $productId)
    {
        $this->productId = $productId;
    }

    public function handle(ProductShowService $productShowService, ProductIndexService $productIndexService): void
    {
        try {
            // Warm product detail, related, similar cache
            // Need a Product model instance or slug. Assuming ProductShowService can fetch by ID for warming.
            // This might require an adjustment in ProductShowService or fetching the product slug/model here.
            // For now, let's assume ProductShowService needs a slug. We need to fetch it.
            $product = \Modules\Product\App\Models\Product::find($this->productId);
            if ($product) {
                // Create a dummy request for ProductShowService if it expects one
                $dummyRequest = new Request();
                $productShowService->show($dummyRequest, $product->slug); // Pass slug
                Log::info("Warmed cache for product ID: {$this->productId}");

                // Warm first page of product index
                // ProductIndexService expects a Request object
                $indexRequest = new Request(['page' => 1]);
                $productIndexService->index($indexRequest);
                Log::info("Warmed cache for product index page 1.");

                // Potentially warm top probable searches - this is complex and out of scope for now
            } else {
                Log::warning("WarmProductCacheJob: Product not found for ID: {$this->productId}");
            }
        } catch (\Exception $e) {
            Log::error("WarmProductCacheJob failed for product ID {$this->productId}: " . $e->getMessage());
        }
    }
}
