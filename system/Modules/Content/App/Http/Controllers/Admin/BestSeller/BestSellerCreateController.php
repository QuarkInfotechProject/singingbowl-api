<?php

namespace Modules\Content\App\Http\Controllers\Admin\BestSeller;

use Modules\Content\App\Http\Requests\BestSeller\BestSellerCreateRequest;
use Modules\Content\Service\Admin\BestSeller\BestSellerCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class BestSellerCreateController extends AdminBaseController
{
    function __construct(private BestSellerCreateService $bestSellerCreateService)
    {
    }

    function __invoke(BestSellerCreateRequest $request)
    {
        $this->bestSellerCreateService->create($request->all(), $request->getClientIp());

        return $this->successResponse('Best seller content has been created successfully.');
    }
}
