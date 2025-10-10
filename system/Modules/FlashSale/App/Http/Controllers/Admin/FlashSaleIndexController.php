<?php
namespace Modules\FlashSale\App\Http\Controllers\Admin;

use Modules\FlashSale\Service\Admin\FlashSaleIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Illuminate\Http\Request;

class FlashSaleIndexController extends AdminBaseController
{
    public function __construct(private flashSaleIndexService $saleIndexService)
    {
    }

    public function __invoke(Request $request)
    {
        $flashSales = $this->saleIndexService->getAll($request);
        return $this->successResponse('Flash sales retrieved successfully.', $flashSales);
    }
}