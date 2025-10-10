<?php

namespace Modules\Blog\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Blog\Service\User\PostIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class PostIndexController extends UserBaseController
{
    function __construct(private PostIndexService $postIndexService)
    {
    }

    function __invoke(Request $request)
    {
        $posts = $this->postIndexService->index($request->get('keyword'));

        return $this->successResponse('Post has been fetched successfully.', $posts);
    }
}
