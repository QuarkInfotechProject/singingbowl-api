<?php

namespace Modules\Content\App\Http\Controllers\Admin\BestSeller;

use Modules\Content\App\Http\Requests\BestSeller\BestSellerUpdateRequest;
use Modules\Content\Service\Admin\BestSeller\BestSellerUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class BestSellerUpdateController extends AdminBaseController
{
    function __construct(private BestSellerUpdateService $bestSellerUpdateService)
    {
    }

    function __invoke(BestSellerUpdateRequest $request)
    {
        $this->bestSellerUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('Best seller content has been updated successfully.');
    }
}
