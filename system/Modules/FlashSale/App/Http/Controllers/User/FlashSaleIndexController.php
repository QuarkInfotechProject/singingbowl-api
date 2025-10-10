<?php
namespace Modules\FlashSale\App\Http\Controllers\User;

use Modules\FlashSale\Service\User\FlashSaleIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class FlashSaleIndexController extends UserBaseController
{
    function __construct(private FlashSaleIndexService $flashSaleIndexService)
    {
    }

    public function __invoke()
    {
        $flashSales = $this->flashSaleIndexService->getAllFlashSales();
        return $this->successResponse('All flash sales have been fetched successfully.', $flashSales);
    }
}