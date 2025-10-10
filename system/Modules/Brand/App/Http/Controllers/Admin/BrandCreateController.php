<?php
namespace Modules\Brand\App\Http\Controllers\Admin;


use Modules\Brand\App\Http\Requests\BrandCreateRequest;
use Modules\Brand\Service\Admin\BrandCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class BrandCreateController extends AdminBaseController
{
    public function __construct(private BrandCreateService $brandCreateService)
    {
    }

    public function __invoke(BrandCreateRequest $request)
    {
        $this->brandCreateService->create($request->all(), $request->getClientIp());


        return $this->successResponse('Brand has been created successfully.');
    }
}
