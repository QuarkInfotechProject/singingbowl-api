<?php
namespace Modules\Others\Service\LimitedTimeDeals\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Others\App\Models\LimitedTimeDeals;
use Modules\Product\App\Models\Product;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class LimitedTimeDealCreateService
{
    public function create(array $data, string $ipAddress)
    {
        $this->validateData($data);

        try {
            DB::beginTransaction();

            $product = $this->getProduct($data['product_uuid']);
            $this->checkProductAvailability($product->id);

            $limitedTimeDeal = LimitedTimeDeals::create([
                'product_id' => $product->id,
                'status' => $data['status'],
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            DB::commit();
            return $limitedTimeDeal;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    private function validateData(array $data): void
    {
        $validator = Validator::make($data, [
            'product_uuid' => 'required|exists:products,uuid',
            'status' => 'required|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function getProduct(string $uuid): Product
    {
        $product = Product::where('uuid', $uuid)->first();

        if (!$product) {
            throw new Exception('Product not found', ErrorCode::NOT_FOUND);
        }

        return $product;
    }

    private function checkProductAvailability(int $productId): void
    {
        $existingDeal = LimitedTimeDeals::where('product_id', $productId)->exists();

        if ($existingDeal) {
            throw new Exception('Product already exists in limited time deals', ErrorCode::BAD_REQUEST);
        }
    }
}