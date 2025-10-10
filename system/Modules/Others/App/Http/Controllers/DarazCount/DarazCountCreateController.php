<?php

namespace Modules\Others\App\Http\Controllers\DarazCount;

use Illuminate\Http\Request;
use Modules\Others\Service\DarazCount\DarazCountCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class DarazCountCreateController extends AdminBaseController
{
    public function __construct(private DarazCountCreateService $darazCountCreateService)
    {
    }

    public function __invoke(Request $request)
    {
        $this->darazCountCreateService->create($request->all());

        return $this->successResponse('Daraz count has been created successfully.');
    }
}
