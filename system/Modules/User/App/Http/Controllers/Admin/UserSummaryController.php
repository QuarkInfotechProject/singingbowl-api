<?php

namespace Modules\User\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Shared\App\Http\Controllers\AdminBaseController;
use Modules\User\Service\Admin\UserSummaryService;

class UserSummaryController extends AdminBaseController
{
    function __construct(private UserSummaryService $userSummaryService)
    {
    }

    function __invoke(Request $request)
    {
        $users = $this->userSummaryService->index(
            $request->get('name'),
            $request->get('sortBy'),
            $request->get('sortDirection'),
            $request->get('page'),
            $request->get('perPage')
        );

        return $this->successResponse('User summary has been fetched successfully.', $users);
    }
}
