<?php

namespace Modules\Others\App\Http\Controllers\ActiveOffers;

use Modules\Others\Service\ActiveOffers\ActiveOfferShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ActiveOfferShowController extends AdminBaseController
{
    function __construct(private ActiveOfferShowService $activeOfferShowService)
    {
    }

    function __invoke(int $id)
    {
        $feature = $this->activeOfferShowService->show($id);

        return $this->successResponse('Active offer has been fetched successfully.', $feature);
    }
}
