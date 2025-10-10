<?php

namespace Modules\Coupon\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Coupon\Service\Admin\CouponDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CouponDestroyController extends AdminBaseController
{
    function __construct(private CouponDestroyService $couponDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->couponDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('Coupon has been deleted successfully.');
    }
}
