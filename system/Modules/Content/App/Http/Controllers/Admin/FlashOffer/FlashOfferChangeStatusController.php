<?php

namespace Modules\Content\App\Http\Controllers\Admin\FlashOffer;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\FlashOffer\FlashOfferChangeStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FlashOfferChangeStatusController extends AdminBaseController
{
    function __construct(private FlashOfferChangeStatusService $flashOfferChangeStatusService)
    {
    }

    function __invoke(Request $request)
    {
        $this->flashOfferChangeStatusService->changeStatus($request->get('id'));

        return $this->successResponse('Flash offer content status has been changed successfully.');
    }
}
