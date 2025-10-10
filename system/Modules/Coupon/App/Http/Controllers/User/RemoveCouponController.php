<?php

namespace Modules\Coupon\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Coupon\Service\User\RemoveCouponService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class RemoveCouponController extends UserBaseController
{
    function __construct(private RemoveCouponService $removeCouponService)
    {
    }

    function __invoke(Request $request)
    {
        try {
            // Let the service handle cart detection
            $cartInfo = $this->removeCouponService->detectCartAndType($request);

            // Prepare data for the removeCoupon method
            $data = [
                'couponCode' => $cartInfo['couponCode'],
                'cartType' => $cartInfo['cartType'],
                'cartId' => $cartInfo['cartId']
            ];

            // Process the coupon removal
            $this->removeCouponService->removeCoupon($data);

            return $this->successResponse('Coupon has been removed successfully.');
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
