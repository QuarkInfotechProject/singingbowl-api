<?php

namespace Modules\Product\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Product\App\Models\Product;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ProductBestSellerService
{
    public function toggleTrending(array $data)
    {
        try {
            DB::beginTransaction();

            $product = Product::where('uuid', $data['Id'])->first();

            if (!$product) {
                throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
            }

            $product->update([
                'best_seller' => $data['bestSeller']
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error updating product trending status: ' . $exception->getMessage(), [
                'exception' => $exception,
                'Id' => $data['uuid'] ?? null
            ]);

            DB::rollBack();
            throw $exception;
        }
    }
}