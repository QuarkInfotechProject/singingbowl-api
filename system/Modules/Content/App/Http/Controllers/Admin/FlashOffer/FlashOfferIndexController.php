<?php

namespace Modules\Content\App\Http\Controllers\Admin\FlashOffer;

use Modules\Content\Service\Admin\FlashOffer\FlashOfferIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FlashOfferIndexController extends AdminBaseController
{
    function __construct(private FlashOfferIndexService $flashOfferIndexService)
    {
    }

    function __invoke()
    {
        $content = $this->flashOfferIndexService->index();

        return $this->successResponse('Flash offer content has been fetched successfully.', $content);
    }
}
