<?php

namespace Modules\Blog\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Blog\Service\Admin\PostDestroyService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class PostDestroyController extends AdminBaseController
{
    function __construct(private PostDestroyService $postDestroyService)
    {
    }

    function __invoke(Request $request)
    {
        $this->postDestroyService->destroy($request->get('id'), $request->getClientIp());

        return $this->successResponse('Post has been deleted successfully.');
    }
}
