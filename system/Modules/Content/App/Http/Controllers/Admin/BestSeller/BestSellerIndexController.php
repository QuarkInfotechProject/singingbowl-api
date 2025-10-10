<?php

namespace Modules\Content\App\Http\Controllers\Admin\BestSeller;

use Modules\Content\Service\Admin\BestSeller\BestSellerIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class BestSellerIndexController extends AdminBaseController
{
    function __construct(private BestSellerIndexService $bestSellerIndexService)
    {
    }

    function __invoke()
    {
        $content = $this->bestSellerIndexService->index();

        return $this->successResponse('Best seller content has been fetched successfully.', $content);
    }
}
