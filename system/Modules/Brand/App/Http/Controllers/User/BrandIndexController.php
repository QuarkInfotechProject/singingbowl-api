<?php
namespace Modules\Brand\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Brand\Service\User\BrandIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class BrandIndexController extends UserBaseController
{
    function __construct(private BrandIndexService $brandIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $brand = $this->brandIndexService->index($request->get('status'), $request->get('name'), $request->get('page'));
        return $this->successResponse('Brand list fetched successfully.', $brand);
    }
}