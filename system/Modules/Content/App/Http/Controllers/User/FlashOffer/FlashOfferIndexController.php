<?php

namespace Modules\Content\App\Http\Controllers\User\FlashOffer;

use Modules\Content\Service\User\FlashOffer\FlashOfferIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class FlashOfferIndexController extends UserBaseController
{
    public function __construct(private FlashOfferIndexService $flashOfferIndexService)
    {
    }

    public function __invoke()
    {
        $contents = $this->flashOfferIndexService->index();

        return $this->successResponse('Flash offer content has been fetched successfully.', $contents);
    }
}
