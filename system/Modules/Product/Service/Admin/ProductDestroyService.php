<?php

namespace Modules\Product\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Product\App\Events\ProductDeleted;
use Modules\Product\App\Models\Product;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ProductDestroyService
{
    function destroy(string $uuid, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $product = Product::where('uuid', $uuid)
                ->first();

            if (!$product) {
                throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
            }

            $productId = $product->id;
            $productName = $product->product_name;

            DB::table('model_files')
                ->where('model_type', get_class($product))
                ->where('model_id', $product->id)
                ->delete();

            $colorOption = $product->options->where('is_color', true)->first();

            if ($colorOption) {
                $colorOptionValues = $colorOption->values;

                foreach ($colorOptionValues as $value) {
                    DB::table('model_files')
                        ->where('model_type', get_class($value))
                        ->where('model_id', $value->id)
                        ->delete();
                }
            }

            $product->delete();

            DB::commit();

            // Dispatch ProductDeleted event to trigger cache invalidation
            Event::dispatch(new ProductDeleted($productId));

            // Log the product destruction
            Event::dispatch(
                new AdminUserActivityLogEvent(
                    'Product destroyed of name: ' . $productName,
                    $productId,
                    ActivityTypeConstant::PRODUCT_DESTROYED,
                    $ipAddress
                )
            );
        } catch (\Exception $exception) {
            Log::error('Error deleting product: ' . $exception->getMessage(), [
                'exception' => $exception,
                'uuid' => $uuid,
                'ip_address' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }
    }
}
