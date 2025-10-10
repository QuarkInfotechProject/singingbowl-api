<?php

namespace Modules\Coupon\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Coupon\App\Models\Coupon;
use Modules\Coupon\DTO\CouponCreateDTO;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class CouponCreateService
{
    function create(CouponCreateDTO $couponCreateDTO, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $coupon = Coupon::create([
                'name' => $couponCreateDTO->name,
                'code' => $couponCreateDTO->code,
                'type' => $couponCreateDTO->type,
                'value' => $couponCreateDTO->value,
                'max_discount' => $couponCreateDTO->maxDiscount,
                'start_date' => $couponCreateDTO->startDate,
                'end_date' => $couponCreateDTO->endDate,
                'is_active' => $couponCreateDTO->isActive,
                'is_public' => $couponCreateDTO->isPublic,
                'is_bulk_offer' => $couponCreateDTO->isBulkOffer,
                'minimum_spend' => $couponCreateDTO->minimumSpend ?? 0,
                'usage_limit_per_coupon' => $couponCreateDTO->usageLimitPerCoupon,
                'usage_limit_per_customer' => $couponCreateDTO->usageLimitPerCustomer,
                'min_quantity' => $couponCreateDTO->minQuantity,
                'apply_automatically' => $couponCreateDTO->applyAutomatically,
                'individual_use_only' => $couponCreateDTO->individualUse,
                'payment_methods' => json_encode($couponCreateDTO->paymentMethods),
            ]);

            // Create an array with the proper keys for saveRelations
            $relationsData = [
                'products' => $couponCreateDTO->products ?? [],
                'excludeProducts' => $couponCreateDTO->excludeProducts ?? [],
                'relatedCoupons' => $couponCreateDTO->relatedCoupons ?? [],
                'excludedCoupons' => $couponCreateDTO->excludedCoupons ?? [],
            ];

            $coupon->saveRelations($relationsData);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Coupon created of code: ' . $coupon->code,
                $coupon->id,
                ActivityTypeConstant::COUPON_CREATED,
                $ipAddress
            )
        );
    }
}
