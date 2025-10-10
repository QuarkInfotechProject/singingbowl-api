<?php

namespace Modules\Order\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Order\Service\User\OrderFailWithCardService;
use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\Shared\Constant\UrlConstant;

class OrderFailWithCardController extends UserBaseController
{
    public function __construct(private OrderFailWithCardService $orderFailWithCardService)
    {
    }

    public function __invoke(Request $request)
    {
        $orderId = $this->orderFailWithCardService->handleFailure($request->all());

        return redirect()->to(UrlConstant::CARD_FAILURE_REDIRECT_URL . $orderId);
    }
}
