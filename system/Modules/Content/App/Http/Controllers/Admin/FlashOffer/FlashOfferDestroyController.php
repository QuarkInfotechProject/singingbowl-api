<?php

namespace Modules\Content\App\Http\Controllers\Admin\FlashOffer;

use Illuminate\Http\Request;
use Modules\Content\Service\Admin\FlashOffer\FlashOfferDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FlashOfferDestroyController extends AdminBaseController
{
    function __construct(private FlashOfferDestroyService $flashOfferDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->flashOfferDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('Content has been deleted successfully.');
    }
}
