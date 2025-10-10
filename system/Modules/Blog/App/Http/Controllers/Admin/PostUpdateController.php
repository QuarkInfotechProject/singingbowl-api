<?php

namespace Modules\Blog\App\Http\Controllers\Admin;

use Modules\Blog\App\Http\Requests\PostUpdateRequest;
use Modules\Blog\Service\Admin\PostUpdateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class PostUpdateController extends AdminBaseController
{
    function __construct(private PostUpdateService $postUpdateService)
    {
    }

    function __invoke(PostUpdateRequest $request)
    {
        $this->postUpdateService->update($request->all(), $request->getClientIp());

        return $this->successResponse('Post has been updated successfully.');
    }
}
