<?php

namespace Modules\Others\App\Http\Controllers\ActiveOffers;

use Modules\Others\Service\ActiveOffers\ActiveOfferIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ActiveOfferIndexController extends AdminBaseController
{
    function __construct(private ActiveOfferIndexService $activeOfferIndexService)
    {
    }

    function __invoke()
    {
        $features = $this->activeOfferIndexService->index();

        return $this->successResponse('Active offers has been fetched successfully.', $features);
    }
}
