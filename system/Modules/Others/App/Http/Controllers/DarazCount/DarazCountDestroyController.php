<?php

namespace Modules\Others\App\Http\Controllers\DarazCount;

use Illuminate\Http\Request;
use Modules\Others\Service\DarazCount\DarazCountDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class DarazCountDestroyController extends AdminBaseController
{
    public function __construct(private DarazCountDestroyService $darazCountDestroyService)
    {
    }

    public function __invoke(Request $request)
    {
        $this->darazCountDestroyService->destroy($request->get('id'));

        return $this->successResponse('Daraz count has been deleted successfully.');
    }
}
