<?php

namespace Modules\Others\Service\LimitedTimeDeals\Admin;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Others\App\Models\LimitedTimeDeals;
use Exception;

class LimitedTimeDealShowService
{
    public function show($id)
    {
        try {
            $deal = LimitedTimeDeals::with([
                'product',
                'product.files',
                'product.reviews',
            ])->findOrFail($id);

            if (!$deal->product) {
                throw new Exception('Product not found for this deal.');
            }

            $product = $deal->product;
            $primaryFile = $product->files->first();

            return [
                'data' => [
                    'product_uuid' => $product->uuid,
                    'product_name' => $product->product_name,
                    'original_price' => $product->original_price,
                    'special_price' => $product->special_price,
                    'special_price_start_date' => $product->special_price_start,
                    'special_price_end_date' => $product->special_price_end,
                    'review_average' => $product->reviews->avg('rating') ?? 0,
                    'review_count' => $product->reviews->count(),
                    'in_stock' => $product->in_stock,
                    'quantity' => $product->quantity,
                    'status' => $deal->status,
                    'sort_order' => $deal->sort_order,
                    'image' => $primaryFile ? $primaryFile->url : null,
                ]
            ];
        } catch (ModelNotFoundException $e) {
            throw new Exception('Limited time deal not found.');
        } catch (Exception $e) {
            throw $e;
        }
    }
}