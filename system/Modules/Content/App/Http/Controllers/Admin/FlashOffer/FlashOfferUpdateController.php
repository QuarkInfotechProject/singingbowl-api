<?php

namespace Modules\Content\App\Http\Controllers\Admin\FlashOffer;

use Modules\Content\App\Http\Requests\FlashOffer\FlashOfferUpdateRequest;
use Modules\Content\Service\Admin\FlashOffer\FlashOfferUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FlashOfferUpdateController extends AdminBaseController
{
    function __construct(private FlashOfferUpdateService $flashOfferUpdateService)
    {
    }

    function __invoke(FlashOfferUpdateRequest $request)
    {
        $this->flashOfferUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('Flash offer content has been updated successfully.');
    }
}
