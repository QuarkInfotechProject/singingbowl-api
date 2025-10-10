<?php

namespace Modules\Coupon\App\Http\Controllers\Admin;

use Modules\Coupon\App\Http\Requests\CouponUpdateRequest;
use Modules\Coupon\DTO\CouponUpdateDTO;
use Modules\Coupon\Service\Admin\CouponUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CouponUpdateController extends AdminBaseController
{
    function __construct(private CouponUpdateService $couponUpdateService)
    {
    }

    function __invoke(CouponUpdateRequest $request)
    {
        $couponUpdateDTO = CouponUpdateDTO::from($request->all());

        $this->couponUpdateService->update($couponUpdateDTO, $request->getClientIp());

        return $this->successResponse('Coupon has been updated successfully.');
    }
}
