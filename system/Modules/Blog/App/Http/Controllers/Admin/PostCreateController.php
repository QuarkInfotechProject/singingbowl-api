<?php

namespace Modules\Blog\App\Http\Controllers\Admin;

use Modules\Blog\App\Http\Requests\PostCreateRequest;
use Modules\Blog\Service\Admin\PostCreateService;
use Modules\Shared\App\Http\Controllers\AdminBaseController;

class PostCreateController extends AdminBaseController
{
    function __construct(private PostCreateService $postCreateService)
    {
    }

    function __invoke(PostCreateRequest $request)
    {
       $this->postCreateService->create($request->all(), $request->getClientIp());

       return $this->successResponse('Post has been created successfully.');
    }
}
