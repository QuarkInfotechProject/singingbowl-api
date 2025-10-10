<?php

namespace Modules\Blog\App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Blog\Service\Admin\PostIndexService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class PostIndexController extends AdminBaseController
{
    function __construct(private PostIndexService $postIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $posts = $this->postIndexService->index($request->all());

        return $this->successResponse('Post has been fetched successfully.', $posts);
    }
}
