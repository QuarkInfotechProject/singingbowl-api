<?php

namespace Modules\Coupon\App\Http\Controllers\Admin;

use Modules\Coupon\Service\Admin\CouponShowNameService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CouponShowCodeController extends AdminBaseController
{
    function __construct(private CouponShowNameService $couponShowNameService)
    {
    }

    function __invoke()
    {
        $coupons = $this->couponShowNameService->index();

        return $this->successResponse('Coupons has been fetched successfully.', $coupons);
    }
}
