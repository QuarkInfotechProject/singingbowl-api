<?php
namespace Modules\Others\App\Http\Controllers\NewArrival\User;

use Illuminate\Http\Request;
use Modules\Others\Service\NewArrival\User\NewArrivalShowService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class NewArrivalShowController extends UserBaseController
{
    public function __construct(private NewArrivalShowService $newArrivalShowService)
    {
    }

    public function __invoke(Request $request)
    {
        $result = $this->newArrivalShowService->getProductsByCategory($request);
        return $this->successResponse('New arrival products retrieved successfully.', $result);
    }
}