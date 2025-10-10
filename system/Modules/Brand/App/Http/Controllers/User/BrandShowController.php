<?php
namespace Modules\Brand\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Brand\Service\User\BrandShowService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class BrandShowController extends UserBaseController
{
    public function __construct(private BrandShowService $brandShowService)
    {
    }

    public function __invoke(Request $request)
    {
        $brand = $this->brandShowService->show($request);
        return $this->successResponse('Brand details fetched successfully.', $brand);
    }
}