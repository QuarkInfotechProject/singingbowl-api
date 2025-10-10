<?php

namespace Modules\Others\App\Http\Controllers\DarazCount;

use Modules\Others\Service\DarazCount\DarazCountShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class DarazCountShowController extends AdminBaseController
{
    public function __construct(private DarazCountShowService $darazCountShowService)
    {
    }

    public function __invoke(int $id)
    {
        $count = $this->darazCountShowService->show($id);

        return $this->successResponse('Daraz count has been fetched successfully.', $count);
    }
}
