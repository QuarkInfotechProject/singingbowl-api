<?php

namespace Modules\Coupon\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Coupon\Service\User\ApplyCouponService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class ApplyCouponController extends UserBaseController
{
    public function __construct(private ApplyCouponService $applyCouponService)
    {
    }

    public function __invoke(Request $request)
    {
        try {
            // Let the service handle cart detection
            $cartInfo = $this->applyCouponService->detectCartAndType($request);

            // Process the coupon
            $coupon = $this->applyCouponService->applyCoupon(
                $cartInfo['couponCode'],
                $cartInfo['cartType'],
                $cartInfo['cartIdentifier']
            );
        } catch (\Exception $e) {
            throw $e;
        }

        return $this->successResponse('Coupon has been applied successfully.', $coupon);
    }
}
