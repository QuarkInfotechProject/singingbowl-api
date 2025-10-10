<?php

namespace Modules\Coupon\App\Http\Controllers\Admin;

use Modules\Coupon\App\Http\Requests\CouponCreateRequest;
use Modules\Coupon\DTO\CouponCreateDTO;
use Modules\Coupon\Service\Admin\CouponCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class CouponCreateController extends AdminBaseController
{
    function __construct(private CouponCreateService $couponCreateService)
    {
    }

    function __invoke(CouponCreateRequest $request)
    {
        $couponCreateDTO = CouponCreateDTO::from($request->all());

        $this->couponCreateService->create($couponCreateDTO, $request->getClientIp());

        return $this->successResponse('Coupon has been created successfully.');
    }
}
