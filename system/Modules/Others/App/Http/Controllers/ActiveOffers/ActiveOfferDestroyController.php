<?php

namespace Modules\Others\App\Http\Controllers\ActiveOffers;

use Illuminate\Http\Request;
use Modules\Others\Service\ActiveOffers\ActiveOfferDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ActiveOfferDestroyController extends AdminBaseController
{
    function __construct(private ActiveOfferDestroyService $activeOfferDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->activeOfferDestroyService->destroy($request->get('id'));

        return $this->successResponse('Active offer has been deleted successfully.');
    }
}
