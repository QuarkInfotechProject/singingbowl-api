<?php

namespace Modules\Blog\App\Http\Controllers\Admin;

use Modules\Blog\Service\Admin\PostShowService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class PostShowController extends AdminBaseController
{
    function __construct(private PostShowService $postShowService)
    {
    }

    function __invoke(int $id)
    {
        $post = $this->postShowService->show($id);

        return $this->successResponse('Post has been fetched successfully.', $post);
    }
}
