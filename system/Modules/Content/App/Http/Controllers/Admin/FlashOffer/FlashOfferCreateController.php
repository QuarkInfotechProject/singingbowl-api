<?php

namespace Modules\Content\App\Http\Controllers\Admin\FlashOffer;

use Modules\Content\App\Http\Requests\FlashOffer\FlashOfferCreateRequest;
use Modules\Content\Service\Admin\FlashOffer\FlashOfferCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FlashOfferCreateController extends AdminBaseController
{
    function __construct(private FlashOfferCreateService $flashOfferCreateService)
    {
    }

    function __invoke(FlashOfferCreateRequest $request)
    {
        $this->flashOfferCreateService->create($request->all(), $request->getClientIp());

        return $this->successResponse('Flash offer content has been created successfully.');
    }
}
