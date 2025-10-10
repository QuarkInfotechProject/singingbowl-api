<?php

namespace Modules\Others\App\Http\Controllers\Giveaway\User;

use Illuminate\Http\Request;
use Modules\Others\Service\Giveaway\User\GiveawayCreateService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class GiveawayCreateController extends UserBaseController
{
    public function __construct(private GiveawayCreateService $giveawayCreateService)
    {
    }

    public function __invoke(Request $request)
    {
        $this->giveawayCreateService->create($request->all());

        return $this->successResponse('You have been entered into the giveaway successfully.');
    }
}
