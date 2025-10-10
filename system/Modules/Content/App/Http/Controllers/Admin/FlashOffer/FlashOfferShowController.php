<?php

namespace Modules\Content\App\Http\Controllers\Admin\FlashOffer;

use Modules\Content\Service\Admin\FlashOffer\FlashOfferShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FlashOfferShowController extends AdminBaseController
{
    function __construct(private FlashOfferShowService $flashOfferShowService)
    {
    }

    function __invoke(int $id)
    {
        $content = $this->flashOfferShowService->show($id);

        return $this->successResponse('Flash offer content has been fetched successfully.', $content);
    }
}
