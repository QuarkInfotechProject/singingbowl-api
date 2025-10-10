<?php

namespace Modules\Product\Service\User;

use Illuminate\Support\Facades\DB;
use Modules\Product\Trait\ValidateProductTrait;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ProductGetBulkOffersService
{
    use ValidateProductTrait;

    function show()
    {
        $url = request('url');
        $price = request('price');

        $product = $this->validateProduct($url);

        $productCoupons = $this->getProductCoupons($product->id);

        if ($productCoupons) {
            return $this->calculateBulkOfferPrices($price, $productCoupons);
        }

        throw new Exception('No bulk offers available for this product.', ErrorCode::NOT_FOUND);
    }

    private function getProductCoupons(int $productId)
    {
        return DB::table('coupons')
            ->join('product_coupons', 'coupons.id', '=', 'product_coupons.coupon_id')
            ->where('product_coupons.product_id', $productId)
            ->where('coupons.is_active', true)
            ->where('coupons.is_bulk_offer', true)
            ->select('coupons.name', 'coupons.code', 'coupons.value', 'coupons.type', 'coupons.min_quantity')
            ->get();
    }

    private function calculateBulkOfferPrices($basePrice, $bulkOffers) {
        $calculatedOffers = [];
        foreach ($bulkOffers as $offer) {
            $discountedPrice = ($offer->type === 'percentage')
                ? $basePrice * (1 - $offer->value / 100)
                : $basePrice - $offer->value;
            $calculatedOffers[] = [
                'name' => $offer->name,
                'minQuantity' => $offer->min_quantity,
                'couponCode' => $offer->code,
                'discountPercentage' => $offer->value,
                'pricePerItem' => round($discountedPrice, 2)
            ];
        }

        return $calculatedOffers;
    }
}
