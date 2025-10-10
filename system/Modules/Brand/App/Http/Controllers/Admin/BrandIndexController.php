<?php
namespace Modules\Brand\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Brand\Service\Admin\BrandIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class BrandIndexController extends AdminBaseController
{
    function __construct(private BrandIndexService $brandIndexService)
    {
    }

    /**
     * Handle the request to fetch the list of brands.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function __invoke(Request $request)
    {
        $data = $request->only(['status', 'name', 'page']);

        $brands = $this->brandIndexService->index($data);

        return $this->successResponse('Brands list fetched successfully.', $brands);
    }
}
