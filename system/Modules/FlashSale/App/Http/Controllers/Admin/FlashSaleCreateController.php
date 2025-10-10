<?php
namespace Modules\FlashSale\App\Http\Controllers\Admin;

use Modules\FlashSale\App\Http\Requests\FlashSaleCreateRequest;
use Modules\FlashSale\Service\Admin\FlashSaleCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class FlashSaleCreateController extends AdminBaseController
{
    function __construct(private FlashSaleCreateService $flashSaleCreateService)
    {
    }

    function __invoke(FlashSaleCreateRequest $request)
    {
        $this->flashSaleCreateService->create($request->all(), $request->getClientIp());
        
        return $this->successResponse('Flash sale has been created successfully.');
    }
}
