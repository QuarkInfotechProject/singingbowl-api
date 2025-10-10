<?php

namespace Modules\Coupon\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Coupon\App\Models\Coupon;
use Modules\Coupon\DTO\CouponUpdateDTO;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CouponUpdateService
{
    function update(CouponUpdateDTO $couponUpdateDTO, string $ipAddress) // Removed $id, get from DTO
    {
        $coupon = Coupon::find($couponUpdateDTO->id);

        if (!$coupon) {
            throw new Exception('Coupon not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $couponUpdateDTO->name,
                'code' => $couponUpdateDTO->code,
                'type' => $couponUpdateDTO->type, // Update type
                'value' => $couponUpdateDTO->value,
                'max_discount' => $couponUpdateDTO->maxDiscount,
                'start_date' => $couponUpdateDTO->startDate,
                'end_date' => $couponUpdateDTO->endDate,
                'is_active' => $couponUpdateDTO->isActive,
                'is_public' => $couponUpdateDTO->isPublic, // Ensure DTO has this
                'is_bulk_offer' => $couponUpdateDTO->isBulkOffer,
                'minimum_spend' => $couponUpdateDTO->minimumSpend ?? 0,
                'usage_limit_per_coupon' => $couponUpdateDTO->usageLimitPerCoupon,
                'usage_limit_per_customer' => $couponUpdateDTO->usageLimitPerCustomer,
                'min_quantity' => $couponUpdateDTO->minQuantity,
                'apply_automatically' => $couponUpdateDTO->applyAutomatically,
                'individual_use_only' => $couponUpdateDTO->individualUse, // Maps to individual_use_only
                // 'is_displayed' => $couponUpdateDTO->isDisplayed, // If you have this
            ];

            if ($couponUpdateDTO->type === Coupon::TYPE_FREE_SHIPPING) {
                $updateData['value'] = 0;
                $updateData['max_discount'] = 0;
            } elseif ($couponUpdateDTO->type === Coupon::TYPE_FIXED_CART) {
                 $updateData['max_discount'] = 0;
            }

            $coupon->update($updateData);

            if (property_exists($couponUpdateDTO, 'paymentMethods')) {
                $this->updatePaymentMethods($couponUpdateDTO->paymentMethods, $coupon);
            }

            // Create an array with the proper keys for saveRelations
            $relationsData = [
                'products' => $couponUpdateDTO->products ?? [],
                'excludeProducts' => $couponUpdateDTO->excludeProducts ?? [],
                'relatedCoupons' => $couponUpdateDTO->relatedCoupons ?? [],
                'excludedCoupons' => $couponUpdateDTO->excludedCoupons ?? [],
            ];

            $coupon->saveRelations($relationsData);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to update Coupon: ' . $exception->getMessage(), [
                'exception' => $exception,
                'couponId' => $coupon->id,
                'couponData' => $couponUpdateDTO->toArray(),
                'ipAddress' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Coupon updated of code: ' . $coupon->code,
                $coupon->id,
                ActivityTypeConstant::COUPON_UPDATED,
                $ipAddress
            )
        );
        return $coupon;
    }

    private function updatePaymentMethods($newPaymentMethods, Coupon $coupon)
    {
        $newPaymentMethods = is_array($newPaymentMethods) ? array_unique(array_filter($newPaymentMethods)) : [];

        try {
            $coupon->update(['payment_methods' => $newPaymentMethods]);
        } catch (\Exception $exception) {
            Log::error('Error updating coupon payment methods: ' . $exception->getMessage(), [
                'coupon_id' => $coupon->id,
                'payment_methods' => $newPaymentMethods
            ]);
            // throw $exception;
        }
    }
}
