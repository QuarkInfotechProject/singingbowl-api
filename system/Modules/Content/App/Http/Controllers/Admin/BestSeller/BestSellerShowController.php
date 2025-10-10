<?php

namespace Modules\Content\App\Http\Controllers\Admin\BestSeller;

use Modules\Content\Service\Admin\BestSeller\BestSellerShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class BestSellerShowController extends AdminBaseController
{
    function __construct(private BestSellerShowService $bestSellerShowService)
    {
    }

    function __invoke(int $id)
    {
        $content = $this->bestSellerShowService->show($id);

        return $this->successResponse('Best seller content has been fetched successfully.', $content);
    }
}
