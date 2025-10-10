<?php

namespace Modules\Others\App\Http\Controllers\DarazCount;

use Modules\Others\Service\DarazCount\DarazCountIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class DarazCountIndexController extends AdminBaseController
{
    public function __construct(private DarazCountIndexService $darazCountIndexService)
    {
    }

    public function __invoke()
    {
        $counts = $this->darazCountIndexService->index();

        return $this->successResponse('Daraz counts has been fetched successfully.', $counts);
    }
}
