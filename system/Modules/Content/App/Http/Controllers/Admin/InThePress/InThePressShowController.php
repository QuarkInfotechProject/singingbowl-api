<?php

namespace Modules\Content\App\Http\Controllers\Admin\InThePress;

use Modules\Content\Service\Admin\InThePress\InThePressShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class InThePressShowController extends AdminBaseController
{
    function __construct(private InThePressShowService $inThePressShowService)
    {
    }

    function __invoke(int $id)
    {
        $content = $this->inThePressShowService->show($id);

        return $this->successResponse('Content has been fetched successfully.', $content);
    }
}
