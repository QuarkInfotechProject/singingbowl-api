<?php
namespace Modules\Others\App\Http\Controllers\NewArrival\Admin;

use Illuminate\Http\Request;
use Modules\Others\Service\NewArrival\Admin\NewArrivalShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class NewArrivalShowController extends AdminBaseController
{
    function __construct(private NewArrivalShowService $newArrivalShowService)
    {
    }

    function __invoke(Request $request, $categoryId)
    {
        $products = $this->newArrivalShowService->getProductsByCategoryId($categoryId);

        return $this->successResponse('New arrival products retrieved successfully.', $products);
    }
}