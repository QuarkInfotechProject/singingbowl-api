<?php

namespace Modules\Product\Service\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Product\App\Models\Product;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ProductQuickUpdateService
{
    function quickUpdate(array $data)
    {
        try {
            DB::beginTransaction();

            $product = Product::where('uuid', $data['uuid'])->first();


            $allOutOfStock = $product->variants->every(function ($variant) {
                return $variant->quantity <= 0;
            });

            if (!$product) {
                throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
            }

            if (!$product->has_variant) {
                $product->update([
                    'original_price' => $data['originalPrice'],
                    'special_price' => $data['specialPrice'],
                    'special_price_start' => $data['specialPriceStart'],
                    'special_price_end' => $data['specialPriceEnd'],
                    'quantity' => $data['quantity'],
                    'in_stock' => $data['inStock'],
                    'status' => $data['status'],
                ]);
            }
            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error changing product status: ' . $exception->getMessage(), [
                'exception' => $exception,
                'uuid' => $product
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
