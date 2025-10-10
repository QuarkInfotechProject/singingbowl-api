<?php

namespace Modules\User\App\Http\Controllers\Admin;

use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\User\Service\Admin\UserShowService;

class UserShowController extends AdminBaseController
{
    function __construct(private UserShowService $userShowService)
    {
    }

    function __invoke($uuid)
    {
        $user = $this->userShowService->show($uuid);

        return $this->successResponse('User has been fetched successfully.', $user);
    }
}
