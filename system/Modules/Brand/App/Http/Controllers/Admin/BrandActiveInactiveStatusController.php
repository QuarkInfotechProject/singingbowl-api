<?php
namespace Modules\Brand\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Brand\Service\Admin\BrandActiveInactiveStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class BrandActiveInactiveStatusController extends AdminBaseController
{
    function __construct(private BrandActiveInactiveStatusService $brandStatusService)
    {
    }

    function __invoke(Request $request)
    {
        $this->brandStatusService->changeStatus($request->get('id'));

        return $this->successResponse('Brand status has been changed successfully.');
    }
}
