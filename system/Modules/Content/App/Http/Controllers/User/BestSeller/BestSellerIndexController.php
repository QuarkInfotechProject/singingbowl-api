<?php

namespace Modules\Content\App\Http\Controllers\User\BestSeller;

use Modules\Content\Service\User\BestSeller\BestSellerIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class BestSellerIndexController extends UserBaseController
{
    public function __construct(private BestSellerIndexService $bestSellerIndexService)
    {
    }

    public function __invoke()
    {
        $contents = $this->bestSellerIndexService->index();

        return $this->successResponse('Best seller content has been fetched successfully.', $contents);
    }
}
