<?php

namespace Modules\Others\App\Http\Controllers\ActiveOffers;

use Illuminate\Http\Request;
use Modules\Others\Service\ActiveOffers\ActiveOfferUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ActiveOfferUpdateController extends AdminBaseController
{
    function __construct(private ActiveOfferUpdateService $activeOfferUpdateService)
    {
    }

    function __invoke(Request $request)
    {
        $this->activeOfferUpdateService->update($request->all());

        return $this->successResponse('Active offer has been updated successfully.');
    }
}
