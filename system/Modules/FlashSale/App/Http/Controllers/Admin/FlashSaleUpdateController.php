<?php
namespace Modules\FlashSale\App\Http\Controllers\Admin;

use Modules\FlashSale\App\Http\Requests\FlashSaleUpdateRequest;
use Modules\FlashSale\Service\Admin\FlashSaleUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FlashSaleUpdateController extends AdminBaseController
{
    function __construct(private FlashSaleUpdateService $flashSaleUpdateService)
    {
    }

    public function __invoke(FlashSaleUpdateRequest $request)
    {
        $this->flashSaleUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('Flash sale has been updated successfully.');
    }
}