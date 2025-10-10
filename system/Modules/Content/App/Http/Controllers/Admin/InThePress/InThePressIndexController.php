<?php

namespace Modules\Content\App\Http\Controllers\Admin\InThePress;

use Modules\Content\Service\Admin\InThePress\InThePressIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class InThePressIndexController extends AdminBaseController
{
    function __construct(private InThePressIndexService $inThePressIndexService)
    {
    }

    function __invoke()
    {
        $contents = $this->inThePressIndexService->index();

        return $this->successResponse('In the press content has been fetched successfully.', $contents);
    }
}
