<?php

namespace Modules\Others\App\Http\Controllers\Giveaway\Admin;

use Illuminate\Http\Request;
use Modules\Others\Service\Giveaway\Admin\GiveawayIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class GiveawayIndexController extends AdminBaseController
{
    function __construct(private GiveawayIndexService $giveawayIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $giveaways = $this->giveawayIndexService->getAll($request);
        return $this->successResponse('Giveaway entries retrieved successfully.', $giveaways);
    }
}