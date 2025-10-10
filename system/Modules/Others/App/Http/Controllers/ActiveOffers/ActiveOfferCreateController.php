<?php

namespace Modules\Others\App\Http\Controllers\ActiveOffers;

use Illuminate\Http\Request;
use Modules\Others\Service\ActiveOffers\ActiveOfferCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ActiveOfferCreateController extends AdminBaseController
{
    function __construct(private ActiveOfferCreateService $activeOfferCreateService)
    {
    }

    function __invoke(Request $request)
    {
        $this->activeOfferCreateService->create($request->all());

        return $this->successResponse('Active offer has been created successfully.');
    }
}
