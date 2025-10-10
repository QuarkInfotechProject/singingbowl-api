<?php

namespace Modules\Others\App\Http\Controllers\LimitedTimeDeals\Admin;

use Illuminate\Http\Request;
use Modules\Others\Service\LimitedTimeDeals\Admin\LimitedTimeDealShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class LimitedTimeDealShowController extends AdminBaseController
{
    public function __construct(private LimitedTimeDealShowService $limitedTimeDealShowService)
    {
    }

    public function __invoke(Request $request, $id)
    {
        $data = $this->limitedTimeDealShowService->show($id);
        return $this->successResponse('Limited time deal details retrieved successfully.', $data);
    }
}