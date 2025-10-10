<?php

namespace Modules\Product\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Product\Service\Admin\ProductReorderService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class ProductReorderController extends AdminBaseController
{
    function __construct(private ProductReorderService $productReorderService)
    {
    }

    function __invoke(Request $request)
    {
        $this->productReorderService->reOrder($request->get('id'), $request->get('sortOrder'));

        return $this->successResponse('Product has been reordered successfully.');
    }
}
