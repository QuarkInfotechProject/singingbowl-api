<?php
namespace Modules\Others\App\Http\Controllers\LimitedTimeDeals\User;

use Illuminate\Http\Request;
use Modules\Others\Service\LimitedTimeDeals\User\LimitedTimeDealIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class LimitedTimeDealIndexController extends UserBaseController
{
    public function __construct(private LimitedTimeDealIndexService $limitedTimeDealUserIndexService)
    {
    }

    public function __invoke(Request $request)
    {
        $data = $this->limitedTimeDealUserIndexService->getAll();
        return $this->successResponse('Limited time deals retrieved successfully.', $data);
    }
}