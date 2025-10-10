<?php

namespace Modules\Blog\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Blog\Service\User\PostShowService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class PostShowController extends UserBaseController
{
    function __construct(private PostShowService $postShowService)
    {
    }

    function __invoke(Request $request)
    {
        $post = $this->postShowService->show($request->query('slug'));

        return $this->successResponse('Post has been fetched successfully.', $post);
    }
}
