<?php

namespace Modules\Order\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Order\Service\User\OrderCompleteWithCardService;
use Modules\Shared\App\Http\Controllers\UserBaseController;
use Modules\Shared\Constant\UrlConstant;

class OrderCompleteWithCardController extends UserBaseController
{
    function __construct(private OrderCompleteWithCardService $orderCompleteWithCardService)
    {
    }

    function __invoke(Request $request)
    {
        $orderId = $this->orderCompleteWithCardService->completeOrderWithCard($request->all());

        return redirect()->to(UrlConstant::CARD_SUCCESS_REDIRECT_URL . $orderId);
    }
}
