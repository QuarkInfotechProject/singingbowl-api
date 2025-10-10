<?php

namespace Modules\Coupon\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Coupon\Service\Admin\CouponChangeStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CouponChangeStatusController extends AdminBaseController
{
    function __construct(private CouponChangeStatusService $couponChangeStatusService)
    {
    }

    function __invoke(Request $request)
    {
        $this->couponChangeStatusService->changeStatus($request->get('id'));

        return $this->successResponse('Coupon status has been changed successfully.');
    }
}
