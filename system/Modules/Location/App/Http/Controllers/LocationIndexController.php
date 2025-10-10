<?php

namespace Modules\Location\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Location\Service\LocationIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class LocationIndexController extends UserBaseController
{
    function __construct(private LocationIndexService $locationIndexService)
    {
    }

    function __invoke()
    {
        $locations = $this->locationIndexService->index();

        return $this->successResponse('Locations has been fetched successfully.', $locations);
    }
}
