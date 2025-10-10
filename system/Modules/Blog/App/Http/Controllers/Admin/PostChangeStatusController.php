<?php

namespace Modules\Blog\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Blog\Service\Admin\PostChangeStatusService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class PostChangeStatusController extends AdminBaseController
{
    function __construct(private PostChangeStatusService $postChangeStatusService)
    {
    }

    function __invoke(Request $request)
    {
        $this->postChangeStatusService->changeStatus($request->get('id'));

        return $this->successResponse('Post status has been changed successfully.');
    }
}
