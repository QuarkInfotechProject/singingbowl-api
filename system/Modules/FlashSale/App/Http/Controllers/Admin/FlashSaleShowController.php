<?php
namespace Modules\FlashSale\App\Http\Controllers\Admin;

use Modules\FlashSale\Service\Admin\FlashSaleShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FlashSaleShowController extends AdminBaseController
{
    public function __construct(private flashSaleShowService $flashSaleShowService)
    {
    }

    public function __invoke($id)
    {
        $flashSale = $this->flashSaleShowService->getById($id);
        return $this->successResponse('Flash sale details retrieved successfully.', $flashSale);
    }
}