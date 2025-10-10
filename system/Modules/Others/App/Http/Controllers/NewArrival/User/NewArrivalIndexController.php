<?php
namespace Modules\Others\App\Http\Controllers\NewArrival\User;

use Modules\Others\Service\NewArrival\User\NewArrivalIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class NewArrivalIndexController extends UserBaseController
{
    public function __construct(private NewArrivalIndexService $newArrivalIndexService)
    {
    }

    public function __invoke()
    {
        $newArrivalCategories = $this->newArrivalIndexService->getAll();
        return $this->successResponse('New arrival categories retrieved successfully.', $newArrivalCategories);
    }
}