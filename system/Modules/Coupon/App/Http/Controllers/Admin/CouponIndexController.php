<?php

namespace Modules\Coupon\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Coupon\Service\Admin\CouponIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CouponIndexController extends AdminBaseController
{
    function __construct(private CouponIndexService $couponIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $coupons = $this->couponIndexService->index($request->get('couponCode'));

        return $this->successResponse('Coupons has been fetched successfully.', $coupons);
    }
}
