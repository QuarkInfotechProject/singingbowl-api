<?php

namespace Modules\Coupon\App\Http\Controllers\Admin;

use Modules\Coupon\Service\Admin\CouponShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CouponShowController extends AdminBaseController
{
    function __construct(private CouponShowService $couponShowService)
    {
    }

    function __invoke(int $id)
    {
        $coupon = $this->couponShowService->show($id);

        return $this->successResponse('Coupon has been fetched successfully.', $coupon);
    }
}
