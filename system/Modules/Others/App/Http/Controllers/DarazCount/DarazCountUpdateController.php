<?php

namespace Modules\Others\App\Http\Controllers\DarazCount;

use Illuminate\Http\Request;
use Modules\Others\Service\DarazCount\DarazCountUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class DarazCountUpdateController extends AdminBaseController
{
    public function __construct(private DarazCountUpdateService $darazCountUpdateService)
    {
    }

    public function __invoke(Request $request)
    {
        $this->darazCountUpdateService->update($request->all());

        return $this->successResponse('Daraz count has been updated successfully.');
    }
}
