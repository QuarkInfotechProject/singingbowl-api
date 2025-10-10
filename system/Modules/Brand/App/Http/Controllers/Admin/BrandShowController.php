<?php
namespace Modules\Brand\App\Http\Controllers\Admin;

use Modules\Brand\Service\Admin\BrandShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class BrandShowController extends AdminBaseController
{
    public function __construct(private BrandShowService $brandShowService)
    {
    }

    public function __invoke(int $id)
    {
            $brand = $this->brandShowService->show($id);

            return $this->successResponse('Brand details fetched successfully.', $brand, 200);

    }
}
