<?php

namespace Modules\Others\Service\DarazCount;

use Illuminate\Support\Facades\DB;
use Modules\Others\App\Models\DarazAnalytics;
use Modules\Product\App\Models\Product;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class DarazCountCreateService
{
    function create($data)
    {
        $product = Product::where('uuid', $data['productId'])->first();

        if (!$product) {
            throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            DarazAnalytics::create([
                'product_id' => $product->id,
                'units_sold' => $data['unitsSold'],
                'reviews_count' => $data['reviewsCount'],
                'link' => $data['link'],
                'is_active' => $data['isActive'],
            ]);

            DB::commit();
        } catch (\Exception $exception) {

            if ($exception->getCode() == 23000) {
                throw new Exception('Daraz count already exists for this product.', ErrorCode::UNPROCESSABLE_CONTENT);
            };

            DB::rollBack();
            throw $exception;
        }
    }
}
