<?php

namespace Modules\Content\App\Http\Controllers\User\InThePress;

use Modules\Content\Service\User\InThePress\InThePressIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class InThePressIndexController extends UserBaseController
{
    function __construct(private InThePressIndexService $inThePressIndexService)
    {
    }

    function __invoke()
    {
        $contents = $this->inThePressIndexService->index();

        return $this->successResponse('Content has been fetched successfully.', $contents);
    }
}
