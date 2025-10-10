<?php

namespace Modules\Coupon\App\Http\Controllers\User;

use Modules\Coupon\Service\User\CouponIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class CouponIndexController extends UserBaseController
{
    function __construct(private CouponIndexService $couponIndexService)
    {
    }

    function __invoke()
    {
        $coupons = $this->couponIndexService->index();

        return $this->successResponse('Coupons has been fetched successfully.', $coupons);
    }
}
